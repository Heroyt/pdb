<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Models\Connection;
use App\Models\ConnectionStorage;
use App\Models\Material;
use App\Request\Connection\ActivateRequest;
use App\Request\Connection\AssignRequest;
use App\Request\Connection\CreateRequest;
use App\Request\Connection\DeactivateRequest;
use App\Request\Connection\DeleteRequest;
use App\Request\Connection\UnassignRequest;
use App\Request\Connection\UpdateMaxStorageRequest;
use App\Request\Connection\UpdateRequest;
use App\Request\Connection\UpdateStorageRequest;
use App\Services\TaskProducer;
use App\Tasks\Connection\ActivateConnection;
use App\Tasks\Connection\AssignConnection;
use App\Tasks\Connection\CreateConnection;
use App\Tasks\Connection\DeactivateConnection;
use App\Tasks\Connection\DeleteConnection;
use App\Tasks\Connection\UnassignConnection;
use App\Tasks\Connection\UpdateConnection;
use App\Tasks\Connection\UpdateConnectionMaxStorage;
use App\Tasks\Connection\UpdateConnectionStorage;
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

class ConnectionController extends Controller
{
    public function __construct(
      private readonly TaskProducer $taskProducer,
    ) {
        parent::__construct();
    }

    #[
      OA\Post(
        path       : '/command/connection',
        operationId: 'createConnection',
        description: 'Create a new connection.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(ref: '#/components/schemas/ConnectionCreateRequest'),
        ),
        tags       : ['command', 'connection'],
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
            $task = $this->taskProducer->push(CreateConnection::class, $createRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection creation was queued',
            values: ['pipeline' => $task->getPipeline(), 'id' => $task->getId(), 'name' => $task->getName()],
          ),
          201
        );
    }

    #[
      OA\Put(
        path       : '/command/connection/{id}',
        operationId: 'updateConnection',
        description: 'Update a connection.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(ref: '#/components/schemas/ConnectionUpdateRequest'),
        ),
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function update(Connection $connection, Request $request) : ResponseInterface {
        try {
            $updateRequest = UpdateRequest::fromRequest($request, $connection);
        } catch (ValidationException $e) {
            return $this->respond(new ErrorResponse('Validation error', ErrorType::VALIDATION, $e->getMessage()), 400);
        }

        try {
            $task = $this->taskProducer->push(UpdateConnection::class, $updateRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the update task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection update was queued',
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
        path       : '/command/connection/{id}',
        operationId: 'deleteConnection',
        description: 'Delete a connection.',
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Cannot delete material.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function delete(Connection $connection) : ResponseInterface {
        // TODO: Check connection storage

        try {
            $task = $this->taskProducer->push(DeleteConnection::class, new DeleteRequest($connection->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the deletion task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection delete was queued',
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
        path       : '/command/connection/{id}/storage',
        operationId: 'updateConnectionStorage',
        description: 'Update a connection storage.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(
                     required  : ['materials'],
                     properties: [
                                   new OA\Property(
                                            'materials',
                                     type : 'array',
                                     items: new OA\Items(
                                              ref: '#/components/schemas/ConnectionUpdateStorageRequest',
                                            ),
                                   ),
                                 ],
                     type      : 'object',
                   ),
        ),
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection or material not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Storage related error.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function updateStorage(Connection $connection, Request $request) : ResponseInterface {
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

        $remainingStorage = $connection->getRemainingStorageCapacity();
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
            foreach ($connection->storage as $test) {
                if ($test->material->id === $materialObj->id) {
                    $storage = $test;
                    break;
                }
            }
            if ($storage === null) {
                $storage = new ConnectionStorage();
                $storage->connection = $connection;
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
            $this->taskProducer->plan(UpdateConnectionStorage::class, $updateRequest);
        }

        if ($remainingStorage < $sizeSum) {
            return $this->respond(
              new ErrorResponse(
                        'Connection does not have enough storage space',
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
                    'Connection storage update was queued',
            values: [
                      'totalSize'        => $sizeSum,
                      'factoryCapacity'  => $connection->storageCapacity,
                      'remainingStorage' => $remainingStorage - $sizeSum,
                    ],
          )
        );
    }

    #[
      OA\Put(
        path       : '/command/connection/{id}/storage-max',
        operationId: 'updateConnectionMaxStorage',
        description: 'Update a connection storage max quantity (what should be automatically loaded).',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(
                     required  : ['materials'],
                     properties: [
                                   new OA\Property(
                                            'materials',
                                     type : 'array',
                                     items: new OA\Items(ref: '#/components/schemas/ConnectionUpdateMaxStorageRequest'),
                                   ),
                                 ],
                     type      : 'object',
                   ),
        ),
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection or material not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Storage related error.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function updateStorageMax(Connection $connection, Request $request) : ResponseInterface {
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

            // Check if material is already stored
            $storage = null;
            foreach ($connection->storage as $test) {
                if ($test->material->id === $materialObj->id) {
                    $storage = $test;
                    break;
                }
            }
            if ($storage === null) {
                $storage = new ConnectionStorage();
                $storage->connection = $connection;
                $storage->material = $materialObj;
            }

            try {
                $updateRequest = UpdateMaxStorageRequest::fromArray($material, $storage);
            } catch (ValidationException $e) {
                return $this->respond(
                  new ErrorResponse(
                            'Invalid input',
                            ErrorType::VALIDATION,
                            $e->getMessage(),
                    values: [
                              'key' => $key,
                            ]
                  ),
                  400
                );
            }
            $this->taskProducer->plan(UpdateConnectionMaxStorage::class, $updateRequest);
        }

        try {
            $this->taskProducer->dispatch();
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the storage update task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse('Connection storage update was queued')
        );
    }

    #[
      OA\Post(
        path       : '/command/connection/{id}/assign',
        operationId: 'assignConnection',
        description: 'Assign a connection - assigned connection should automatically transport material.',
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function assign(Connection $connection) : ResponseInterface {
        try {
            $task = $this->taskProducer->push(AssignConnection::class, new AssignRequest($connection->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the assigning task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection assigning was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }

    #[
      OA\Post(
        path       : '/command/connection/{id}/unassign',
        operationId: 'unassignConnection',
        description: 'Unassign a connection - unassigned connection should not automatically transport material.',
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function unassign(Connection $connection) : ResponseInterface {
        try {
            $task = $this->taskProducer->push(UnassignConnection::class, new UnassignRequest($connection->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the unassigning task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection unassigning was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }

    #[
      OA\Post(
        path       : '/command/connection/{id}/activate',
        operationId: 'activateConnection',
        description: 'Activate a connection - active connection is currently on route.',
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function activate(Connection $connection) : ResponseInterface {
        try {
            $task = $this->taskProducer->push(ActivateConnection::class, new ActivateRequest($connection->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the activation task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection activation was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }

    #[
      OA\Post(
        path       : '/command/connection/{id}/deactivate',
        operationId: 'deactivateConnection',
        description: 'Deactivate a connection - deactivated connection is currently not on route.',
        tags       : ['command', 'connection'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Connection not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function deactivate(Connection $connection) : ResponseInterface {
        try {
            $task = $this->taskProducer->push(DeactivateConnection::class, new DeactivateRequest($connection->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the deactivation task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Connection deactivation was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }
}
