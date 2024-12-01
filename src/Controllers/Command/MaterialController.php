<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Models\Material;
use App\Request\Material\CreateRequest;
use App\Request\Material\DeleteRequest;
use App\Request\Material\UpdateRequest;
use App\Services\TaskProducer;
use App\Tasks\Material\CreateMaterial;
use App\Tasks\Material\DeleteMaterial;
use App\Tasks\Material\UpdateMaterial;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Dto\SuccessResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\Jobs\Exception\JobsException;

class MaterialController extends Controller
{
    public function __construct(
      private readonly TaskProducer $taskProducer,
    ) {
        parent::__construct();
    }

    #[
      OA\Post(
        path       : '/command/material',
        operationId: 'createMaterial',
        description: 'Create a new material.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(ref: '#/components/schemas/MaterialCreateRequest'),
        ),
        tags       : ['command', 'material'],
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
            $task = $this->taskProducer->push(CreateMaterial::class, $createRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Material creation was queued',
            values: ['pipeline' => $task->getPipeline(), 'id' => $task->getId(), 'name' => $task->getName()],
          ),
          201
        );
    }

    #[
      OA\Put(
        path       : '/command/material/{id}',
        operationId: 'updateMaterial',
        description: 'Update a material.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(ref: '#/components/schemas/MaterialUpdateRequest'),
        ),
        tags       : ['command', 'material'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Material ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Material not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function update(Material $material, Request $request) : ResponseInterface {
        try {
            $updateRequest = UpdateRequest::fromRequest($request, $material);
        } catch (ValidationException $e) {
            return $this->respond(new ErrorResponse('Validation error', ErrorType::VALIDATION, $e->getMessage()), 400);
        }

        try {
            $task = $this->taskProducer->push(UpdateMaterial::class, $updateRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the update task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Material update was queued',
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
        path       : '/command/material/{id}',
        operationId: 'deleteMaterial',
        description: 'Delete a material.',
        tags       : ['command', 'material'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Material ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Material not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Cannot delete material.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function delete(Material $material) : ResponseInterface {
        // TODO: Check if material is not stocked anywhere

        try {
            $task = $this->taskProducer->push(DeleteMaterial::class, new DeleteRequest($material->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the deletion task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Material delete was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }
}
