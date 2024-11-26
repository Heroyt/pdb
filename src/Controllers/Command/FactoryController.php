<?php

declare(strict_types=1);

namespace App\Controllers\Command;

use App\Services\TaskProducer;
use App\Tasks\Factory\CreateFactory;
use App\Tasks\Factory\CreateFactoryPayload;
use Lsr\Core\Controllers\Controller;
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
        private readonly TaskProducer $taskProducer,
    ) {
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
            return $this->respond(new ErrorResponse('Capacity must be a valid positive integer.', ErrorType::VALIDATION), 400);
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
}
