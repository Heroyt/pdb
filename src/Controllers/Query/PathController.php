<?php
declare(strict_types=1);

namespace App\Controllers\Query;

use App\Models\Connection;
use App\Models\Factory;
use App\Services\Provider\PathFinder;
use Lsr\Core\Caching\Cache;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;

class PathController extends Controller
{

    public function __construct(
      private readonly PathFinder $pathFinder,
      private readonly Cache      $cache,
    ) {
        parent::__construct();
    }

    #[
      OA\Get(
        path       : '/query/path',
        operationId: 'findShortestPath',
        description: 'Find paths between 2 factories based on their speed and capacity.',
        tags       : ['query', 'path'],
      ),
      OA\QueryParameter(
        name       : 'from',
        description: 'Start factory ID',
        required   : true,
        schema     : new OA\Schema(type: 'integer', minimum: 1),
      ),
      OA\QueryParameter(
        name       : 'to',
        description: 'End factory ID',
        required   : true,
        schema     : new OA\Schema(type: 'integer', minimum: 1),
      ),
      OA\QueryParameter(
        name       : 'count',
        description: 'How many paths should be found',
        required   : false,
        schema     : new OA\Schema(type: 'integer', default: 1, minimum: 1),
      ),
      OA\QueryParameter(
        name       : 'capacity',
        description: 'If set, filters out all connections with storage capacity less than specified.',
        required   : false,
        schema     : new OA\Schema(type: 'integer', default: 1, minimum: 1),
      ),
      OA\Response(
        response   : 200,
        description: 'List of found paths',
        content    : new OA\JsonContent(
          type : 'array',
          items: new OA\Items(ref: '#/components/schemas/ConnectionPath')
        )
      )
    ]
    public function findShortestPaths(Request $request) : ResponseInterface {
        // Validate "from" parameter
        $fromId = $request->getGet('from');
        if (!is_numeric($fromId)) {
            return $this->respond(
              new ErrorResponse(
                '"from" parameter is required and must be a number.',
                ErrorType::VALIDATION,
              ),
              400
            );
        }
        try {
            $from = Factory::get((int) $fromId);
        } catch (ModelNotFoundException) {
            return $this->respond(
              new ErrorResponse(
                        '"from" factory does not exist.',
                        ErrorType::NOT_FOUND,
                values: [
                          'id' => (int) $fromId,
                        ]
              ),
              404,
            );
        }

        // Validate "to" parameter
        $toId = $request->getGet('to');
        if (!is_numeric($toId)) {
            return $this->respond(
              new ErrorResponse(
                '"to" parameter is required and must be a number.',
                ErrorType::VALIDATION,
              ),
              400
            );
        }
        try {
            $to = Factory::get((int) $toId);
        } catch (ModelNotFoundException) {
            return $this->respond(
              new ErrorResponse(
                        '"to" factory does not exist.',
                        ErrorType::NOT_FOUND,
                values: [
                          'id' => (int) $toId,
                        ]
              ),
              404,
            );
        }

        $count = $request->getGet('count', 1);
        if (!is_numeric($count) || $count < 1) {
            return $this->respond(
              new ErrorResponse(
                '"count" parameter must be a positive number.',
                ErrorType::VALIDATION,
              ),
              400
            );
        }
        $count = (int) $count;
        assert($count > 0);

        $minCapacity = $request->getGet('capacity');
        if ($minCapacity !== null) {
            if (!is_numeric($minCapacity) || $minCapacity < 1) {
                return $this->respond(
                  new ErrorResponse(
                    '"capacity" parameter must be a positive number.',
                    ErrorType::VALIDATION,
                  ),
                  400
                );
            }
            $minCapacity = (int) $minCapacity;

            $paths = $this->cache->load(
              sprintf('path-capacity.%d-%d.%d.%d', $from->id, $to->id, $minCapacity, $count),
              fn() => $this->pathFinder->findPathWithCapacity($from, $to, $minCapacity, $count),
              /** @phpstan-ignore argument.type */
              [
                $this->cache::Tags   => [
                  'paths',
                  'paths/capacity',
                  Connection::TABLE,
                ],
                $this->cache::Expire => '1 hours',
              ]
            );
            return $this->respond($paths);
        }

        $paths = $this->cache->load(
          sprintf('path.%d-%d.%d', $from->id, $to->id, $count),
          function () use ($from, $to, $count) {
              $this->pathFinder->indexGraph();
              return $this->pathFinder->findPaths($from, $to, $count);
          },
          /** @phpstan-ignore argument.type */
          [
            $this->cache::Tags   => [
              'paths',
              Connection::TABLE,
            ],
            $this->cache::Expire => '1 hours',
          ]
        );
        return $this->respond($paths);
    }

}