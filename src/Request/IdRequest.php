<?php

declare(strict_types=1);

namespace App\Request;

abstract class IdRequest
{
    public function __construct(
        public int $id,
    ) {
    }
}
