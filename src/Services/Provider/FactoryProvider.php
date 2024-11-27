<?php

declare(strict_types=1);

namespace App\Services\Provider;

use App\EventStore\Events\Factory\CreateEvent;
use App\EventStore\Events\Factory\DeleteEvent;
use App\EventStore\Events\Factory\UpdateEvent;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Factory;
use App\Request\Factory\UpdateRequest;
use Dibi\DriverException;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Lsr\Core\DB;
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
    ) {}

    /**
     * @throws ModelCreationException
     * @throws ValidationException
     * @throws DriverException
     * @throws Throwable
     */
    public function createFactory(string $name, int $capacity = 50) : Factory {
        $factory = new Factory();
        $factory->name = $name;
        $factory->storageCapacity = $capacity;
        DB::getConnection()->begin();
        $this->writeFactory($factory);
        $result = $this->streams->appendEvent(CreateEvent::fromFactory($factory), $factory::TABLE.'_'.$factory->id);
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
    public function updateFactory(UpdateRequest $request) : Factory {
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
        $result = $this->streams->appendEvent($event, $factory::TABLE.'_'.$factory->id);
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
    public function deleteFactory(Factory $factory) : void {
        DB::getConnection()->begin();
        // TODO: Delete storage and connections
        if (!$factory->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete factory: '.$factory->name);
        }
        try {
            $this->client->writeTransaction(fn(TransactionInterface $tsx) => $this->deleteFactoryNode($tsx, $factory));
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent(DeleteEvent::fromFactory($factory), $factory::TABLE.'_'.$factory->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    private function writeFactory(Factory $factory) : void {
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

    public function deleteFactoryNode(TransactionInterface $tsx, Factory $factory) : void {
        $result = $tsx->run('MATCH (f:Factory {id: $id}) DETACH DELETE f', ['id' => $factory->id]);
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function createFactoryNode(TransactionInterface $tsx, Factory $factory) : void {
        $result = $tsx->run(
          'MERGE (f:Factory {id: $id}) SET f.name = $name return f',
          ['id' => $factory->id, 'name' => $factory->name]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }
}
