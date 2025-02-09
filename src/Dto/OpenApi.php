<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Info(
    version    : '1.0',
    description: 'API documentation.',
    title      : 'PDB API',
)]
#[OA\Server(url: 'https://localhost', description: 'Local')]
class OpenApi
{
}
