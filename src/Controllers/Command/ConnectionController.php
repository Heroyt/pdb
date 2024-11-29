<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Models\Connection;
use App\Request\Connection\CreateRequest;
use App\Request\Connection\DeleteRequest;
use App\Request\Connection\UpdateRequest;
use App\Services\TaskProducer;
use App\Tasks\Connection\CreateConnection;
use App\Tasks\Connection\DeleteConnection;
use App\Tasks\Connection\UpdateConnection;
use Lsr\Core\Controllers\Controller;
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
        parameter  : 'id',
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
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
        parameter  : 'id',
        name       : 'id',
        description: 'Connection ID',
        in         : 'path',
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

}
