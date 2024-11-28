<?php
declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ProcessDto')]
class Process
{

    /** @var ProcessPart[] */
    #[OA\Property]
    public array $outputs = [];
    /** @var ProcessPart[] */
    #[OA\Property]
    public array $inputs = [];

    public function addOutput(ProcessPart $part): void {
        $this->outputs[] = $part;
    }

    public function addInput(ProcessPart $part): void {
        $this->inputs[] = $part;
    }

}