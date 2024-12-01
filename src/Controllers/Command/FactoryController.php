<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Models\Factory;
use App\Models\FactoryStorage;
use App\Models\Material;
use App\Request\Factory\CreateRequest;
use App\Request\Factory\DeleteRequest;
use App\Request\Factory\UpdateRequest;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\ConnectionProvider;
use App\Services\TaskProducer;
use App\Tasks\Factory\CreateFactory;
use App\Tasks\Factory\DeleteFactory;
use App\Tasks\Factory\UpdateFactory;
use App\Tasks\Factory\UpdateFactoryStorage;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ModelNotFoundException;
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
          content: new OA\JsonContent(ref: '#/components/schemas/FactoryCreateRequest',),
        ),
        tags       : ['command', 'factory'],
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 201),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function create(Request $request) : ResponseInterface {
        try {
            $createRequest = CreateRequest::fromRequest($request);
        } catch (ValidationException $e) {
            return $this->respond(new ErrorResponse('Validation error', ErrorType::VALIDATION, $e->getMessage()), 400);
        }

        try {
            $task = $this->taskProducer->push(CreateFactory::class, $createRequest);
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
        name       : 'id',
        description: 'Factory ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Factory not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function update(Factory $factory, Request $request) : ResponseInterface {
        try {
            $updateRequest = UpdateRequest::fromRequest($request, $factory);
        } catch (ValidationException $e) {
            return $this->respond(new ErrorResponse('Validation error', ErrorType::VALIDATION, $e->getMessage()), 400);
        }

        try {
            $task = $this->taskProducer->push(UpdateFactory::class, $updateRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the update task', exception: $e), 500);
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
        name       : 'id',
        description: 'Factory ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Factory not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Cannot delete factory.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function delete(Factory $factory) : ResponseInterface {
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
            $task = $this->taskProducer->push(DeleteFactory::class, new DeleteRequest($factory->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the deletion task', exception: $e), 500);
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

    #[
      OA\Put(
        path       : '/command/factory/{id}/storage',
        operationId: 'updateFactoryStorage',
        description: 'Update a factory storage.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(
                     required  : ['materials'],
                     properties: [
                                   new OA\Property(
                                            'materials',
                                     type : 'array',
                                     items: new OA\Items(
                                              required  : ['id', 'quantity'],
                                              properties: [
                                                            new OA\Property(
                                                                           'id',
                                                              description: 'Material ID.',
                                                              type       : 'integer',
                                                            ),
                                                            new OA\Property(
                                                                           'quantity',
                                                              description: 'Quantity difference (positive for adding stock, negative for removing).',
                                                              type       : 'integer',
                                                            ),
                                                          ],
                                              type      : 'object'
                                            ),
                                   ),
                                 ],
                     type      : 'object',
                   ),
        ),
        tags       : ['command', 'factory'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Factory ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Factory or material not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Storage related error.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function updateStorage(Factory $factory, Request $request) : ResponseInterface {
        $materials = $request->getPost('materials', []);
        if (!is_array($materials)) {
            return $this->respond(
              new ErrorResponse(
                'Invalid input',
                ErrorType::VALIDATION,
                'materials must be an array of objects'
              ),
              400
            );
        }

        $remainingStorage = $factory->getRemainingStorageCapacity();
        $sizeSum = 0;

        /**
         * @var mixed $material
         */
        foreach ($materials as $key => $material) {
            if (!is_array($material)) {
                return $this->respond(
                  new ErrorResponse(
                    'Invalid input',
                    ErrorType::VALIDATION,
                    'materials['.$key.'] must be an objects'
                  ),
                  400
                );
            }
            if (
              !isset($material['id'])
              || !is_numeric($material['id'])
            ) {
                return $this->respond(
                  new ErrorResponse(
                    'Invalid input',
                    ErrorType::VALIDATION,
                    'materials['.$key.'].id must be an integer'
                  ),
                  400
                );
            }
            try {
                $materialObj = Material::get((int) $material['id']);
            } catch (ModelNotFoundException) {
                return $this->respond(
                  new ErrorResponse(
                            'Material not found',
                            ErrorType::NOT_FOUND,
                    values: [
                              'key' => $key,
                              'id'  => (int) $material['id'],
                            ]
                  ),
                  404
                );
            }

            if (!isset($material['quantity']) || !is_numeric($material['quantity'])) {
                return $this->respond(
                  new ErrorResponse(
                    'Invalid input',
                    ErrorType::VALIDATION,
                    'materials['.$key.'].quantity must be an integer'
                  ),
                  400
                );
            }

            $quantity = (int) $material['quantity'];
            $sizeSum += $materialObj->size * $quantity;

            // Check if material is already stored
            $storage = null;
            foreach ($factory->storage as $test) {
                if ($test->material->id === $materialObj->id) {
                    $storage = $test;
                    break;
                }
            }
            if ($storage === null) {
                $storage = new FactoryStorage();
                $storage->facility = $factory;
                $storage->material = $materialObj;
            }

            $storage->quantity += $quantity;
            if ($storage->quantity < 0) {
                return $this->respond(
                  new ErrorResponse(
                            'Overall storage quantity cannot be negative',
                            ErrorType::VALIDATION,
                    values: [
                              'finalQuantity' => $storage->quantity,
                              'diff'          => $quantity,
                              'key'           => $key,
                            ]
                  ),
                  412
                );
            }

            $updateRequest = new UpdateStorageRequest($storage);
            $updateRequest->quantity = $quantity;
            $this->taskProducer->plan(UpdateFactoryStorage::class, $updateRequest);
        }

        if ($remainingStorage < $sizeSum) {
            return $this->respond(
              new ErrorResponse(
                        'Factory does not have enough storage space',
                        ErrorType::VALIDATION,
                values: [
                          'remainingStorage' => $remainingStorage,
                          'totalSize'        => $sizeSum,
                        ],
              ),
              412
            );
        }

        try {
            $this->taskProducer->dispatch();
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the storage update task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Factory storage update was queued',
            values: [
                      'totalSize'        => $sizeSum,
                      'factoryCapacity'  => $factory->storageCapacity,
                      'remainingStorage' => $remainingStorage - $sizeSum,
                    ],
          )
        );
    }
}
