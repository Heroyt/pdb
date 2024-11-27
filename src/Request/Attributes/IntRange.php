<?php
declare(strict_types=1);

namespace App\Request\Attributes;

use Attribute;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Models\Attributes\Validation\Validator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class IntRange implements Validator
{

    public function __construct(
      public ?int $min = null,
      public ?int $max = null,
    ){}

    /**
     * @inheritDoc
     */
    public function validateValue(mixed $value, object | string $class, string $property) : void {
        if (!is_int($value)) {
            throw ValidationException::createWithValue(
              'Property '.(is_string($class) ? $class : $class::class).'::'.$property.' must be an int. (value: %s)',
              $value
            );
        }

        if ($this->min !== null && $value < $this->min) {
            throw ValidationException::createWithValue(
              'Number must be larger then '.$this->min.'. (value: %s)',
              $value
            );
        }
        if ($this->max !== null && $value > $this->max) {
            throw ValidationException::createWithValue(
              'Number must be lower then '.$this->max.'. (value: %s)',
              $value
            );
        }
    }
}