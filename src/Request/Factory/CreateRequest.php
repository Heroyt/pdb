<?php

declare(strict_types=1);

namespace App\Request\Factory;

final readonly class CreateRequest
{
    /**
     * @param  non-empty-string  $name
     * @param  int<1,max>  $capacity
     */
    public function __construct(
        public string $name,
        public int $capacity,
    ) {
    }
}
