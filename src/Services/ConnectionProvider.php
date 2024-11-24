<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ModelCreationException;
use App\Models\Connection;
use App\Models\Factory;
use Dibi\DriverException;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Logger;
use Throwable;

readonly class ConnectionProvider
{

    public function __construct(
      private ClientInterface $client,
      private FactoryProvider $factoryProvider,
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
        return $this->updateConnection($start, $end, $connection);
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function updateConnection(Factory $start, Factory $end, Connection $connection) : Connection {
        DB::getConnection()->begin();
        if (!$connection->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to save connection to database');
        }

        try {
            $this->client->writeTransaction(
              function (TransactionInterface $tsx) use ($connection, $start, $end) {
                  // Make sure that the nodes exist
                  $this->factoryProvider->createFactoryNode($tsx, $start);
                  $this->factoryProvider->createFactoryNode($tsx, $end);
                  // Connect the nodes
                  $this->createConnectionEdge($tsx, $start, $end, $connection);
              }
            );
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }

        DB::getConnection()->commit();
        return $connection;
    }

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

}