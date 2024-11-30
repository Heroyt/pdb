<?php
declare(strict_types=1);

namespace App\Controllers\Query;

use App\Models\Material;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Requests\Request;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;

class MaterialController extends Controller
{

    #[
      OA\Get(
        path       : '/query/material',
        operationId: 'Find materials',
        tags       : ['query', 'material'],
      ),
      OA\QueryParameter(
        name       : 'name',
        description: 'Filter materials by name',
        required   : false,
        schema     : new OA\Schema(type: 'string')
      ),
      OA\Response(
        response   : 200,
        description: 'List of materials',
        content    : new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Material')),
      )
    ]
    public function find(Request $request) : ResponseInterface {
        $query = Material::query();

        $name = $request->getGet('name');
        if (is_string($name) && !empty($name)) {
            $query->where('name LIKE %~like~', $name);
        }

        return $this->respond(array_values($query->get()));
    }

    #[
      OA\Get(
        path       : '/query/material/{id}',
        operationId: 'Get material',
        tags       : ['query', 'material'],
      ),
      OA\PathParameter(
        name       : 'id',
        description: 'Material ID',
        required   : true,
        schema     : new OA\Schema(type: 'integer', minimum: 1)
      ),
      OA\Response(
        ref     : '#/components/schemas/Material',
        response: 200
      )
    ]
    public function show(Material $material) : ResponseInterface {
        return $this->respond($material);
    }

}