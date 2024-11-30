<?php
declare(strict_types=1);

namespace App\Services\Provider;

use App\Dto\ConnectionPath;
use App\Dto\FactoryConnectionWithCost;
use App\Models\Connection;
use App\Models\Factory;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Laudis\Neo4j\Types\CypherList;
use Laudis\Neo4j\Types\CypherMap;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Logger;

readonly class PathFinder
{

    /**
     * @var int Neo4j does not preserve any properties on found relations apart from cost.
     * We need to encode the relation ID into the cost by multiplying the cost by a constant and adding the ID to the result, so we can extract both the cost and ID.
     * The relation ID is necessary to distinguish between parallel relations between nodes.
     *
     * @warning Must be larger that the largest connection ID, or it would break.
     */
    private const int COST_MULTIPLIER = 10000;

    /**
     * @param  ClientInterface<SummarizedResult<BoltResult>>  $client
     */
    public function __construct(
      private ClientInterface $client,
    ) {}

    /**
     * Create a Neo4j projection that would be searched to find the paths.
     */
    public function indexGraph(bool $recreate = false) : bool {
        // TODO: Invalidate projection automatically if the relations change
        return $this->client->writeTransaction(
          function (TransactionInterface $tsx) use ($recreate) {
              /** @var SummarizedResult<BoltResult> $result */
              $result = $tsx->run("RETURN gds.graph.exists('factories')::Boolean");
              /** @var CypherMap<bool> $map */
              $map = $result->getResults()->first();
              $exists = $map->getAsBool('gds.graph.exists(\'factories\')::Boolean');
              if ($exists) {
                  if (!$recreate) {
                      return false;
                  }
                  $this->dropProjection($tsx);
              }
              $result = $tsx->run(
                <<<CYPHER
                MATCH (source:Factory)-[r:Connection]->(target:Factory)
                RETURN gds.graph.project(
                  'factories',
                  source,
                  target,
                  {
                    relationshipProperties: r { .id, .speed, .storage, cost: (\$multiplier * round(r.speed / r.storage)) + r.id }
                  }
                )
                CYPHER,
                [
                  'multiplier' => self::COST_MULTIPLIER
                ]
              );
              new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
              return true;
          }
        );
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     * @return void
     */
    public function dropProjection(TransactionInterface $tsx): void {
        $result = $tsx->run("CALL gds.graph.drop('factories', false) YIELD graphName");
        // Results are yielded → must get the first result to actually drop the graph.
        $result->getResults()->first();
    }

    /**
     * @param  Factory  $from
     * @param  Factory  $to
     * @param  int  $minCapacity
     * @param  int<1,max>  $k How many paths to find
     * @return ConnectionPath[]
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    public function findPathWithCapacity(Factory $from, Factory $to, int $minCapacity, int $k = 1) : array {
        $projectionName = 'factories-capacity-'.$minCapacity;
        $this->client->writeTransaction(
          static function (TransactionInterface $tsx) use ($projectionName, $minCapacity) {
              $result = $tsx->run("CALL gds.graph.drop('\$name', false) YIELD graphName", ['name' => $projectionName]);
              // Results are yielded → must get the first result to actually drop the graph.
              /** @var CypherList<string> $results */
              $results = $result->getResults();
              if ($results->count() > 0) {
                  $results->first();
              }
              $result = $tsx->run(
                <<<CYPHER
                MATCH (source:Factory)-[r:Connection]->(target:Factory)
                WHERE r.storage >= \$minCapacity
                RETURN gds.graph.project(
                  \$name,
                  source,
                  target,
                  {
                    relationshipProperties: r { .id, .speed, .storage, cost: (\$multiplier * r.speed) + r.id }
                  }
                )
                CYPHER,
                [
                  'multiplier' => self::COST_MULTIPLIER,
                  'name' => $projectionName,
                  'minCapacity' => $minCapacity,
                ]
              );
              new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
          }
        );

        return $this->searchYens($projectionName, $from, $to, $k);
    }

    /**
     * @param int<1,max> $k
     * @return ConnectionPath[]
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    private function searchYens(string $projectionName, Factory $from, Factory $to, int $k = 1) : array {
        $result = $this->client->run(
          <<<CYPHER
            MATCH (source:Factory {id: \$from}), (target:Factory {id: \$to})
            CALL gds.shortestPath.yens.stream(\$name, {
                sourceNode: source,
                targetNode: target,
                k:\$k,
                relationshipWeightProperty: 'cost'
            })
            YIELD index, sourceNode, targetNode, totalCost, nodeIds, costs, path
            RETURN
                index,
                totalCost,
                [nodeId IN nodeIds | gds.util.asNode(nodeId).id] AS nodeIds,
                costs
            ORDER BY index
            CYPHER,
          [
            'name' => $projectionName,
            'from' => $from->id,
            'to' => $to->id,
            'k' => $k,
          ]
        );

        $paths = [];
        /** @var CypherMap<mixed> $path */
        foreach ($result->getResults() as $path) {
            $pathObj = new ConnectionPath($from, $to, round($path->getAsFloat('totalCost') / self::COST_MULTIPLIER));
            $nodeIds = $path->getAsArrayList('nodeIds');
            $costs = $path->getAsArrayList('costs');
            $prevId = null;
            $totalCost = 0.0;
            foreach ($nodeIds as $key => $nodeId) {
                if ($prevId === null) {
                    // First element in path
                    $prevId = $nodeId;
                    continue;
                }

                // Decode real cost and connection ID from the cost
                $mergedCost = $costs[$key] - $totalCost;
                $totalCost = $costs[$key];
                $cost = round($mergedCost / self::COST_MULTIPLIER);
                $connectionId = (int) ($mergedCost - ($cost * self::COST_MULTIPLIER));

                $connection = new FactoryConnectionWithCost(
                  Factory::get($prevId),
                  Connection::get($connectionId),
                  Factory::get($nodeId),
                );
                $connection->cost = $cost;
                $pathObj->path[] = $connection;
                $prevId = $nodeId;
            }
            $paths[] = $pathObj;
        }
        return $paths;
    }

    /**
     * @param  Factory  $from
     * @param  Factory  $to
     * @param  int<1,max>  $k How many paths to find
     * @return ConnectionPath[]
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    public function findPaths(Factory $from, Factory $to, int $k = 1) : array {
        return $this->searchYens('factories', $from, $to, $k);
    }

}