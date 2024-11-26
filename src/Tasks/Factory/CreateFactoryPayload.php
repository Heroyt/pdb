<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

final readonly class CreateFactoryPayload
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
