<?php

declare(strict_types=1);

namespace App\Services\Provider;

use App\Dto\Db\StoppedFactory as StoppedFactoryRow;
use App\Dto\StoppedFactory;
use App\Enums\Direction;
use App\EventStore\Events\Factory\CreateEvent;
use App\EventStore\Events\Factory\DeleteEvent;
use App\EventStore\Events\Factory\UpdateEvent;
use App\EventStore\Events\Factory\UpdateStorage;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Factory;
use App\Models\FactoryStorage;
use App\Models\Material;
use App\Models\Process;
use App\Request\Factory\UpdateRequest;
use App\Request\Factory\UpdateStorageRequest;
use Dibi\DriverException;
use Dibi\Exception;
use Generator;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Logger;
use Throwable;

readonly class FactoryProvider
{
    /**
     * @param  ClientInterface<SummarizedResult<BoltResult>>  $client
     */
    public function __construct(
        private ClientInterface $client,
        private Streams         $streams,
    ) {
    }

    /**
     * @throws ModelCreationException
     * @throws ValidationException
     * @throws DriverException
     * @throws Throwable
     */
    public function createFactory(string $name, int $capacity = 50): Factory {
        $factory = new Factory();
        $factory->name = $name;
        $factory->storageCapacity = $capacity;
        DB::getConnection()->begin();
        $this->writeFactory($factory);
        $result = $this->streams->appendEvent(CreateEvent::fromFactory($factory), $factory::TABLE . '_' . $factory->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $factory;
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    private function writeFactory(Factory $factory): void {
        if (!$factory->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to create factory');
        }
        try {
            $this->client->writeTransaction(fn(TransactionInterface $tsx) => $this->createFactoryNode($tsx, $factory));
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function createFactoryNode(TransactionInterface $tsx, Factory $factory): void {
        $result = $tsx->run(
            'MERGE (f:Factory {id: $id}) SET f.name = $name return f',
            ['id' => $factory->id, 'name' => $factory->name]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function updateFactory(UpdateRequest $request): Factory {
        $changes = $request->getChanges();
        if (empty($changes)) {
            return $request->entity; // Nothing changed
        }
        DB::getConnection()->begin();
        $event = UpdateEvent::fromFactory($request->entity);
        foreach ($changes as $property => $value) {
            $event->{$property} = $value;
        }
        $factory = $request->apply();
        $this->writeFactory($factory);
        $result = $this->streams->appendEvent($event, $factory::TABLE . '_' . $factory->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $factory;
    }

    /**
     * @throws DriverException
     * @throws ModelDeleteException
     * @throws Throwable
     */
    public function deleteFactory(Factory $factory): void {
        DB::getConnection()->begin();
        // TODO: Delete storage and connections
        if (!$factory->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete factory: ' . $factory->name);
        }
        try {
            $this->client->writeTransaction(fn(TransactionInterface $tsx) => $this->deleteFactoryNode($tsx, $factory));
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent(DeleteEvent::fromFactory($factory), $factory::TABLE . '_' . $factory->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function deleteFactoryNode(TransactionInterface $tsx, Factory $factory): void {
        $result = $tsx->run('MATCH (f:Factory {id: $id}) DETACH DELETE f', ['id' => $factory->id]);
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws ModelDeleteException
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function updateStorage(UpdateStorageRequest $request): void {
        DB::getConnection()->begin();
        $storage = $request->entity;
        $factory = $storage->facility;

        if (isset($storage->id)) {
            // Fetch current data
            $storage->fetch(true);
        } else { // New entity
            // Reset quantity -> will be calculated later
            $storage->quantity = 0;
        }

        $quantityDiff = $request->quantity;
        $newQuantity = $storage->quantity + $quantityDiff;
        if ($newQuantity < 0) {
            $quantityDiff = -$storage->quantity; // Set quantity to 0
        } elseif ($newQuantity > $factory->storageCapacity) {
            // Cannot go over storage capacity
            $quantityDiff -= $newQuantity - $factory->storageCapacity;
        }

        $storage->quantity += $quantityDiff;

        $event = new UpdateStorage($factory->id, $storage->material->id, $quantityDiff);

        if (!$storage->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to update factory storage');
        }
        $result = $this->streams->appendEvent($event, $factory::TABLE . '_' . $factory->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @return Generator<StoppedFactory>
     * @throws Exception
     */
    public function findStoppedFactories(): Generator {
        $query = DB::select(
            [Factory::TABLE, 'f'],
            <<<SQL
            f.*, 
            COALESCE(SUM(s.quantity * m.size),0) as [stored],
            %sql as [out_size],
            CASE 
            WHEN COUNT(DISTINCT p.id_material) = SUM(CASE WHEN COALESCE(ps.quantity, 0) >= p.quantity THEN 1 ELSE 0 END) 
                THEN 1 
                ELSE 0 
            END AS has_all_materials
            SQL,
            DB::select([Process::TABLE, 'po'], 'COALESCE(SUM(po.quantity * pm.size), 0)')
            ->join(Material::TABLE, 'pm')
            ->on('po.id_material = pm.id_material')
            ->where('po.id_factory = f.id_factory AND po.type = %s', Direction::OUT->value)
            ->fluent
        )
                   ->cacheTags(
                       'models',
                       Factory::TABLE,
                       Factory::TABLE . '/query',
                       ...Factory::CACHE_TAGS
                   )
                   ->leftJoin(FactoryStorage::TABLE, 's')
                   ->on('f.id_factory = s.id_factory')
                   ->leftJoin(Material::TABLE, 'm')
                   ->on('m.id_material = s.id_material')
                   ->leftJoin(Process::TABLE, 'p')
                   ->on('(p.id_factory = f.id_factory AND p.type = %s)', Direction::IN->value)
                   ->leftJoin(FactoryStorage::TABLE, 'ps')
                   ->on('(f.id_factory = ps.id_factory AND p.id_material = ps.id_material)')
                   ->groupBy('f.id_factory')
                   ->having('has_all_materials = 0 OR (storage_capacity - stored) < out_size');

        foreach ($query->fetchIteratorDto(StoppedFactoryRow::class) as $row) {
            yield StoppedFactory::fromStoppedFactoryRow($row);
        }
    }
}
