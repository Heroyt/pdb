<?php
declare(strict_types=1);

namespace App\Controllers\Query;

use App\Dto\FactoryFull;
use App\Enums\Direction;
use App\Models\Factory;
use App\Models\Process;
use App\Services\Provider\FactoryProvider;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;

class FactoryController extends Controller
{

    public function __construct(
      private readonly FactoryProvider $factoryProvider,
    ) {
        parent::__construct();
    }

    #[
      OA\Get(
        path       : '/query/factory',
        operationId: 'Find factories',
        tags       : ['query', 'factory'],
      ),
      OA\QueryParameter(
        name       : 'name',
        description: 'Factory name',
        required   : false,
        schema     : new OA\Schema(type: 'string'),
      ),
      OA\QueryParameter(
        name       : 'input[]',
        description: 'Input material IDs',
        required   : false,
        schema     : new OA\Schema(
          description: 'Material IDs',
          type       : 'array',
          items      : new OA\Items(
                         type: 'integer',
                       )
        ),
      ),
      OA\QueryParameter(
        name       : 'output[]',
        description: 'Output material IDs',
        required   : false,
        schema     : new OA\Schema(
          description: 'Material IDs',
          type       : 'array',
          items      : new OA\Items(
                         type: 'integer',
                       )
        ),
      ),
      OA\Response(
        response   : 200,
        description: 'List of found factories',
        content    : new OA\JsonContent(
          type : 'array',
          items: new OA\Items(
                   ref: '#/components/schemas/FactoryFullDto'
                 )
        ),
      )
    ]
    public function find(Request $request) : ResponseInterface {
        $query = Factory::query();

        $name = $request->getGet('name');
        if (is_string($name) && !empty($name)) {
            $query->where('a.name LIKE %~like~', $name);
        }

        $inputs = $request->getGet('input');
        if (is_numeric($inputs) && !empty($inputs)) {
            $inputs = [$inputs];
        }
        if (is_array($inputs)) {
            $inputs = array_filter(array_map('intval', $inputs));
            if (!empty($inputs)) {
                $query
                  ->join(Process::TABLE, '[in]')
                  ->on(
                    '(a.id_factory = [in].id_factory AND [in].type = %s AND [in].id_material IN %in)',
                    Direction::IN->value,
                    $inputs
                  );
            }
        }

        $outputs = $request->getGet('output');
        if (is_numeric($outputs) && !empty($outputs)) {
            $outputs = [$outputs];
        }
        if (is_array($outputs)) {
            $outputs = array_filter(array_map('intval', $outputs));
            if (!empty($outputs)) {
                $query
                  ->join(Process::TABLE, '[out]')
                  ->on(
                    '(a.id_factory = [out].id_factory AND [out].type = %s AND [out].id_material IN %in)',
                    Direction::OUT->value,
                    $outputs
                  );
            }
        }


        $factories = $query->get();

        return $this->respond(
          array_values(
            array_map(
              static fn(Factory $row) => FactoryFull::fromFactory($row),
              $factories
            )
          )
        );
    }


    #[
      OA\Get(
        path       : '/query/factory/{id}',
        operationId: 'Get factory',
        tags       : ['query', 'factory'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Factory id',
        required   : true,
        schema     : new OA\Schema(
          type: 'integer',
        )
      ),
      OA\Response(
        ref     : '#/components/schemas/FactoryFullDto',
        response: 200
      ),
      OA\Response(
        ref        : '#/components/schemas/ErrorResponse',
        response   : 404,
        description: 'Factory not found'
      )
    ]
    public function show(Factory $factory) : ResponseInterface {
        return $this->respond(FactoryFull::fromFactory($factory));
    }

    #[
      OA\Get(
        path       : '/query/factory/stopped',
        operationId: 'Get stopped factories',
        tags       : ['query', 'factory'],
      ),
      OA\Response(
        response: 200,
        description: 'List of stopped factories',
        content    : new OA\JsonContent(
          type : 'array',
          items: new OA\Items(
            ref: '#/components/schemas/StoppedFactoryDto'
                 )
        )
      ),
      OA\Response(
        ref        : '#/components/schemas/ErrorResponse',
        response   : 404,
        description: 'Factory not found'
      )
    ]
    public function stoppedFactories() : ResponseInterface {
        $factories = $this->factoryProvider->findStoppedFactories();
        return $this->respond(iterator_to_array($factories));
    }

}