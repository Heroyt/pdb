<?php

declare(strict_types=1);

namespace App\Request;

class IdRequest
{
    public function __construct(
        public int $id,
    ) {
    }
}
