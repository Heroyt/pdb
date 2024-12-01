<?php
declare(strict_types=1);

namespace App\Controllers\Command;

use App\Enums\Direction;
use App\Models\Factory;
use App\Models\Material;
use App\Models\Process;
use App\Request\Process\CreateRequest;
use App\Request\Process\DeleteRequest;
use App\Services\Provider\ProcessProvider;
use App\Services\TaskProducer;
use App\Tasks\Process\CreateProcess;
use App\Tasks\Process\DeleteProcess;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Dto\SuccessResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\Jobs\Exception\JobsException;

class ProcessController extends Controller
{

    public function __construct(
      private readonly TaskProducer    $taskProducer,
      private readonly ProcessProvider $processProvider,
    ) {
        parent::__construct();
    }

    #[
      OA\Post(
        path       : '/command/factory/{id}/process',
        operationId: 'Create process',
        description: 'Create a new process.',
        requestBody: new OA\RequestBody(
          content: new OA\JsonContent(ref: '#/components/schemas/ProcessCreateRequest'),
        ),
        tags       : ['command', 'process', 'factory'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Factory ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 201),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 400, description: 'Bad request.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function create(Factory $factory, Request $request) : ResponseInterface {
        try {
            $createRequest = CreateRequest::fromRequest($request);
        } catch (ValidationException $e) {
            return $this->respond(
              new ErrorResponse(
                'Validation error',
                ErrorType::VALIDATION,
                $e->getMessage(),
              ),
              400,
            );
        }
        $createRequest->factory = $factory;

        $material = Material::get($createRequest->material);
        if ($material->wildcard) {
            if ($createRequest->type !== Direction::IN) {
                return $this->respond(
                  new ErrorResponse(
                    'Wildcard material must only be used as an input to process',
                    ErrorType::VALIDATION,
                  ),
                  400
                );
            }
            try {
                $this->processProvider->validateFactoryWildcardProcess($factory, $material);
            } catch (ValidationException $e) {
                return $this->respond(
                  new ErrorResponse(
                    $e->getMessage(),
                    ErrorType::VALIDATION,
                  ),
                  400,
                );
            }
        }

        try {
            $task = $this->taskProducer->push(CreateProcess::class, $createRequest);
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the creation task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Process creation was queued',
            values: ['pipeline' => $task->getPipeline(), 'id' => $task->getId(), 'name' => $task->getName()],
          ),
          201
        );
    }

    #[
      OA\Delete(
        path       : '/command/process/{id}',
        operationId: 'deleteProcess',
        description: 'Delete a process.',
        tags       : ['command', 'process'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Process ID',
        in         : 'path',
        required   : true,
        schema     : new OA\Schema(type: 'integer'),
      ),
      OA\Response(ref: '#/components/schemas/SuccessResponse', response: 200),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 404, description: 'Process not found.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 412, description: 'Cannot delete process.'),
      OA\Response(ref: '#/components/schemas/ErrorResponse', response: 500, description: 'Internal server error.'),
    ]
    public function delete(Process $process) : ResponseInterface {
        try {
            $task = $this->taskProducer->push(DeleteProcess::class, new DeleteRequest($process->id));
        } catch (JobsException $e) {
            return $this->respond(new ErrorResponse('Failed to queue the deletion task', exception: $e), 500);
        }

        return $this->respond(
          new SuccessResponse(
                    'Process delete was queued',
            values: [
                      'pipeline' => $task->getPipeline(),
                      'id'       => $task->getId(),
                      'name'     => $task->getName(),
                    ],
          )
        );
    }

}