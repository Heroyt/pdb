<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Models\Factory;
use App\Request\Factory\UpdateRequest;
use App\Services\Provider\ConnectionProvider;
use App\Services\TaskProducer;
use App\Tasks\Factory\CreateFactory;
use App\Tasks\Factory\CreateFactoryPayload;
use App\Tasks\Factory\DeleteFactory;
use App\Tasks\Factory\UpdateFactory;
use App\Tasks\IdPayload;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Dto\SuccessResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\Jobs\Exception\JobsException;

class FactoryController extends Controller
{
    public function __construct(
        private readonly TaskProducer       $taskProducer,
        private readonly ConnectionProvider $connectionProvider,
    ) {
        parent::__construct();
    }

    #[
      OA\Post(
          path       : '/command/factory',
          operationId: 'createFactory',
          description: 'Create a new factory.',
          requestBody: new OA\RequestBody(
              content: new OA\JsonContent(
                  properties: [
                                   new OA\Property(
                                       property   : 'name',
                                       description: 'Factory name',
                                       type       : 'string',
                                       nullable   : false,
                                   ),
                                   new OA\Property(
                                       property   : 'capacity',
                                       description: 'Storage capacity',
                                       type       : 'integer',
                                       default    : 50,
                                       nullable   : true,
                                   ),
                                 ],
                  type      : 'object'
              ),
          ),
          tags       : ['command', 'factory'],
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 201),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function create(Request $request): ResponseInterface {
        $name = $request->getPost('name');
        $capacity = $request->getPost('capacity', 50);

        if (!is_string($name) || empty($name)) {
            return $this->respond(new ErrorResponse('Missing required "name" parameter.', ErrorType::VALIDATION), 400);
        }

        if (!is_numeric($capacity) || ($capacity = (int) $capacity) < 1) {
            return $this->respond(
                new ErrorResponse('Capacity must be a valid positive integer.', ErrorType::VALIDATION),
                400
            );
        }

        try {
            $task = $this->taskProducer->push(CreateFactory::class, new CreateFactoryPayload($name, (int) $capacity));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
            new SuccessResponse(
                'Factory creation was queued',
                values: ['pipeline' => $task->getPipeline(), 'id' => $task->getId(), 'name' => $task->getName()],
            ),
            201
        );
    }

    #[
      OA\Put(
          path       : '/command/factory/{id}',
          operationId: 'updateFactory',
          description: 'Update a factory.',
          requestBody: new OA\RequestBody(
              content: new OA\JsonContent(ref: '#/components/schemas/FactoryUpdateRequest'),
          ),
          tags       : ['command', 'factory'],
      ),
      OA\PathParameter(
          parameter  : 'id',
          name       : 'id',
          description: 'Factory ID',
          in         : 'path',
          schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Factory not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function update(Factory $factory, Request $request): ResponseInterface {
        try {
            $updateRequest = UpdateRequest::fromRequest($request, $factory);
        } catch (ValidationException $e) {
            return $this->respond(new ErrorResponse('Validation error', ErrorType::VALIDATION, $e->getMessage()), 400);
        }

        try {
            $task = $this->taskProducer->push(UpdateFactory::class, $updateRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
            new SuccessResponse(
                'Factory update was queued',
                values: [
                      'changes'  => $updateRequest->getChanges(),
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
            )
        );
    }

    #[
      OA\Delete(
          path       : '/command/factory/{id}',
          operationId: 'deleteFactory',
          description: 'Delete a factory.',
          tags       : ['command', 'factory'],
      ),
      OA\PathParameter(
          parameter  : 'id',
          name       : 'id',
          description: 'Factory ID',
          in         : 'path',
          schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Factory not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Cannot delete factory.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function delete(Factory $factory): ResponseInterface {
        // Validate that no connections to the factory exist
        $connections = $this->connectionProvider->findConnectionsStartingAt($factory);
        $foundConnections = [];
        foreach ($connections as $connection) {
            $foundConnections[] = [
              'connectionId' => $connection->connection->id,
              'startId'      => $connection->start->id,
              'endId'        => $connection->end->id,
            ];
        }
        $connections = $this->connectionProvider->findConnectionsEndingAt($factory);
        foreach ($connections as $connection) {
            $foundConnections[] = [
              'connectionId' => $connection->connection->id,
              'startId'      => $connection->start->id,
              'endId'        => $connection->end->id,
            ];
        }
        if (!empty($foundConnections)) {
            return $this->respond(
                new ErrorResponse(
                    'Found at least one connection for the given factory.',
                    ErrorType::VALIDATION,
                    'Make sure to delete all connections before deleting the factory.',
                    values: [
                          'connections' => $foundConnections,
                        ],
                ),
                412
            );
        }

        try {
            $task = $this->taskProducer->push(DeleteFactory::class, new IdPayload($factory->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
            new SuccessResponse(
                'Factory delete was queued',
                values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
            )
        );
    }
}
