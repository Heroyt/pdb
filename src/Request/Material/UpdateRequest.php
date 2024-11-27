<?php
declare(strict_types=1);

namespace App\Request\Material;

use App\Models\Material;

/**
 * @extends \App\Request\UpdateRequest<Material>
 */
class UpdateRequest extends \App\Request\UpdateRequest
{

    public string $name;
    public int $size;
    public bool $wildcard;

    public function __construct(Material $material){
        parent::__construct($material);
    }

}