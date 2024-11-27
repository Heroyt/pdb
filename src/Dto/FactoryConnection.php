<?php
declare(strict_types=1);

namespace App\Dto;

use App\Models\Connection;
use App\Models\Factory;

class FactoryConnection
{

    public function __construct(
      public Factory $start,
      public Connection $connection,
      public Factory $end,
    ){}

}