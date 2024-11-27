<?php
declare(strict_types=1);

namespace App\Tasks;

class IdPayload
{

    public function __construct(
      public int $id,
    ) {}

}