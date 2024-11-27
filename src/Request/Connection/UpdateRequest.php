<?php
declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\Connection;

/**
 * @extends \App\Request\UpdateRequest<Connection>
 */
class UpdateRequest extends \App\Request\UpdateRequest
{

    public bool $assigned;
    public bool $active;
    public int $speed;
    public int $storageCapacity;

    public function __construct(Connection $entity) {
        parent::__construct($entity);
    }

}