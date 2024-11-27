<?php

declare(strict_types=1);

namespace App\Services;

use App\EventStore\Events\Connection\CreateEvent;
use App\EventStore\Events\Connection\DeleteEvent;
use App\EventStore\Events\Connection\UpdateEvent;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Connection;
use App\Models\Factory;
use App\Request\Connection\UpdateRequest;
use Dibi\DriverException;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Lsr\Core\DB;
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
    ) {}

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function createConnection(Factory $start, Factory $end, int $speed, int $capacity) : Connection {
        $connection = new Connection();
        $connection->speed = $speed;
        $connection->storageCapacity = $capacity;
        DB::getConnection()->begin();
        $this->writeConnection($connection, $start, $end);
        $result = $this->streams->appendEvent(
          CreateEvent::fromConnection($connection, $start, $end),
          $connection::TABLE.'_'.$connection->id
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
    public function writeConnection(Connection $connection, ?Factory $start = null, ?Factory $end = null) : void {
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
                  }
                  else {
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
    ) : void {
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
    ) : void {
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
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function deleteConnectionEdge(
      TransactionInterface $tsx,
      Connection           $connection
    ) : void {
        $result = $tsx->run(
          'MATCH ()-(r:Connection {id: $id})->() DETACH DELETE r',
          [
            'id'       => $connection->id,
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
    public function updateConnection(UpdateRequest $request) : Connection {
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
        $result = $this->streams->appendEvent($event, $connection::TABLE.'_'.$connection->id);
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
    public function deleteFactory(Connection $connection) : void {
        DB::getConnection()->begin();
        if (!$connection->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete connection: '.$connection->id);
        }
        try {
            $this->client->writeTransaction(fn(TransactionInterface $tsx) => $this->deleteConnectionEdge($tsx, $connection));
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        $result = $this->streams->appendEvent(DeleteEvent::fromConnection($connection), $connection::TABLE.'_'.$connection->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }
}
