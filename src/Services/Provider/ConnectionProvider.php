<?php

declare(strict_types=1);

namespace App\Services\Provider;

use App\Dto\FactoryConnection;
use App\EventStore\Events\Connection\ActivateEvent;
use App\EventStore\Events\Connection\AssignEvent;
use App\EventStore\Events\Connection\CreateEvent;
use App\EventStore\Events\Connection\DeactivateEvent;
use App\EventStore\Events\Connection\DeleteEvent;
use App\EventStore\Events\Connection\UnassignEvent;
use App\EventStore\Events\Connection\UpdateEvent;
use App\EventStore\Events\Connection\UpdateMaxStorageEvent;
use App\EventStore\Events\Connection\UpdateStorageEvent;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Connection;
use App\Models\Factory;
use App\Request\Connection\UpdateMaxStorageRequest;
use App\Request\Connection\UpdateRequest;
use App\Request\Connection\UpdateStorageRequest;
use Dibi\DriverException;
use Generator;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Laudis\Neo4j\Types\Node;
use Laudis\Neo4j\Types\Relationship;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Logger;
use Throwable;

readonly class ConnectionProvider
{
    /**
     * @param  ClientInterface<SummarizedResult<BoltResult>>  $client
     */
    public function __construct(
        private ClientInterface $client,
        private FactoryProvider $factoryProvider,
        private Streams         $streams,
    ) {
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function createConnection(Factory $start, Factory $end, int $speed, int $capacity): Connection {
        $connection = new Connection();
        $connection->speed = $speed;
        $connection->storageCapacity = $capacity;
        DB::getConnection()->begin();
        $this->writeConnection($connection, $start, $end);
        $result = $this->streams->appendEvent(
            CreateEvent::fromConnection($connection, $start, $end),
            $connection::TABLE . '_' . $connection->id
        );
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $connection;
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function writeConnection(Connection $connection, ?Factory $start = null, ?Factory $end = null): void {
        if (!$connection->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to save connection to database');
        }

        try {
            $this->client->writeTransaction(
                function (TransactionInterface $tsx) use ($connection, $start, $end) {
                    if ($start !== null && $end !== null) {

                        // Make sure that the nodes exist
                        $this->factoryProvider->createFactoryNode($tsx, $start);
                        $this->factoryProvider->createFactoryNode($tsx, $end);
                        // Connect the nodes
                        $this->createConnectionEdge($tsx, $start, $end, $connection);
                    } else {
                        $this->updateConnectionEdge($tsx, $connection);
                    }
                }
            );
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function createConnectionEdge(
        TransactionInterface $tsx,
        Factory              $start,
        Factory              $end,
        Connection           $connection
    ): void {
        $result = $tsx->run(
            'MATCH (f1:Factory {id: $id1}), (f2:Factory {id: $id2}) MERGE (f1)-[r:Connection {id: $id}]->(f2) SET r.assigned = $assigned, r.active = $active, r.speed = $speed, r.storage = $capacity RETURN r',
            [
            'id'       => $connection->id,
            'id1'      => $start->id,
            'id2'      => $end->id,
            'assigned' => $connection->assigned,
            'active'   => $connection->active,
            'speed'    => $connection->speed,
            'capacity' => $connection->storageCapacity,
            ]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function updateConnectionEdge(
        TransactionInterface $tsx,
        Connection           $connection
    ): void {
        $result = $tsx->run(
            'MATCH ()-(r:Connection {id: $id})->() SET r.assigned = $assigned, r.active = $active, r.speed = $speed, r.storage = $capacity RETURN r',
            [
            'id'       => $connection->id,
            'assigned' => $connection->assigned,
            'active'   => $connection->active,
            'speed'    => $connection->speed,
            'capacity' => $connection->storageCapacity,
            ]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function updateConnection(UpdateRequest $request): Connection {
        $changes = $request->getChanges();
        if (empty($changes)) {
            return $request->entity; // No changes
        }
        DB::getConnection()->begin();
        $event = UpdateEvent::fromConnection($request->entity);
        foreach ($changes as $property => $value) {
            $event->{$property} = $value;
        }
        $connection = $request->apply();
        $this->writeConnection($connection);
        $result = $this->streams->appendEvent($event, $connection::TABLE . '_' . $connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $connection;
    }

    /**
     * @throws DriverException
     * @throws ModelDeleteException
     * @throws Throwable
     */
    public function deleteConnection(Connection $connection): void {
        DB::getConnection()->begin();
        if (!$connection->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete connection: ' . $connection->id);
        }
        try {
            $this->client->writeTransaction(
                fn(TransactionInterface $tsx) => $this->deleteConnectionEdge($tsx, $connection)
            );
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent(
            DeleteEvent::fromConnection($connection),
            $connection::TABLE . '_' . $connection->id
        );
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function deleteConnectionEdge(
        TransactionInterface $tsx,
        Connection           $connection
    ): void {
        $result = $tsx->run(
            'MATCH ()-(r:Connection {id: $id})->() DETACH DELETE r',
            [
            'id' => $connection->id,
            ]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }

    /**
     * @param  Factory  $start
     * @return Generator<FactoryConnection>
     */
    public function &findConnectionsStartingAt(Factory $start): Generator {
        $result = $this->client->run(
            'MATCH (s:Factory {id: $id})-[r:Connection]->(e:Factory) RETURN s, r, e',
            [
            'id' => $start->id,
            ]
        );

        foreach ($result->getResults() as $record) {
            $connectionData = $record->get('r');
            assert($connectionData instanceof Relationship);
            $connectionId = $connectionData->getProperty('id');
            assert(is_int($connectionId));
            $endData = $record->get('e');
            assert($endData instanceof Node);
            $endId = $endData->getProperty('id');
            assert(is_int($endId));

            try {
                $connection = Connection::get($connectionId);
                $endFactory = Factory::get($endId);

                yield new FactoryConnection($start, $connection, $endFactory);
            } catch (ModelNotFoundException | ValidationException) {
            }
        }
    }

    /**
     * @param  Factory  $end
     * @return Generator<FactoryConnection>
     */
    public function &findConnectionsEndingAt(Factory $end): Generator {
        $result = $this->client->run(
            'MATCH (s:Factory)-[r:Connection]->(e:Factory {id: $id}) RETURN s, r, e',
            [
            'id' => $end->id,
            ]
        );

        foreach ($result->getResults() as $record) {
            $startData = $record->get('s');
            assert($startData instanceof Node);
            $startId = $startData->getProperty('id');
            assert(is_int($startId));
            $connectionData = $record->get('r');
            assert($connectionData instanceof Relationship);
            $connectionId = $connectionData->getProperty('id');
            assert(is_int($connectionId));

            try {
                $connection = Connection::get($connectionId);
                $startFactory = Factory::get($startId);

                yield new FactoryConnection($startFactory, $connection, $end);
            } catch (ModelNotFoundException | ValidationException) {
            }
        }
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
        $connection = $storage->connection;

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
        } else if ($newQuantity > $connection->storageCapacity) {
            // Cannot go over storage capacity
            $quantityDiff -= $newQuantity - $connection->storageCapacity;
        }

        $storage->quantity += $quantityDiff;

        $event = new UpdateStorageEvent($connection->id, $storage->material->id, $quantityDiff);

        if (!$storage->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to update connection storage');
        }
        $result = $this->streams->appendEvent($event, $connection::TABLE . '_' . $connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function updateMaxStorage(UpdateMaxStorageRequest $request): void {
        DB::getConnection()->begin();
        if (isset($request->entity->id)) {
            // Fetch current data
            $request->entity->fetch(true);
        }
        $storage = $request->apply();
        $connection = $storage->connection;
        $event = new UpdateMaxStorageEvent($connection->id, $storage->material->id, $request->maxQuantity);
        if (!$storage->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to update connection storage');
        }
        $result = $this->streams->appendEvent($event, $connection::TABLE . '_' . $connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function setActive(Connection $connection, bool $active): Connection {
        DB::getConnection()->begin();
        $event = $active ? new ActivateEvent($connection->id) : new DeactivateEvent($connection->id);
        $connection->active = $active;
        try {
            $this->writeConnection($connection);
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent($event, $connection::TABLE . '_' . $connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $connection;
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function setAssigned(Connection $connection, bool $assigned): Connection {
        DB::getConnection()->begin();
        $event = $assigned ? new AssignEvent($connection->id) : new UnassignEvent($connection->id);
        $connection->assigned = $assigned;
        try {
            $this->writeConnection($connection);
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent($event, $connection::TABLE . '_' . $connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $connection;
    }
}
