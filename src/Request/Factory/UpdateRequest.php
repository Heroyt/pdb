<?php
declare(strict_types=1);

namespace App\Request\Factory;

use App\Models\Factory;

/**
 * @extends \App\Request\UpdateRequest<Factory>
 */
class UpdateRequest extends \App\Request\UpdateRequest
{

    public string $name;
    public int $storageCapacity;

    public function __construct(
      Factory $entity,
    ){
        parent::__construct($entity);
    }
}