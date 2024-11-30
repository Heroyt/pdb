<?php

declare(strict_types=1);

namespace App\Request\Attributes;

use Attribute;
use InvalidArgumentException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Models\Attributes\Validation\Validator;
use Lsr\Core\Models\Model;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ModelExists implements Validator
{
    /**
     * @param  class-string<Model>  $class
     */
    public function __construct(
        public string $class,
    ) {
        if (!class_exists($class) || !is_a($class, Model::class, true)) {
            throw new InvalidArgumentException('Class "' . $class . '" does not exist or is not a valid Model class.');
        }
    }

    /**
     * @inheritDoc
     */
    public function validateValue(mixed $value, object | string $class, string $property): void {
        if (!is_int($value)) {
            throw ValidationException::createWithValue(
                'Property ' . (is_string($class) ? $class : $class::class) . '::' . $property . ' must be an int. (value: %s)',
                $value
            );
        }

        if (!$this->class::exists($value)) {
            throw ValidationException::createWithValue(
                'Model (' . $this->class . ') of given ID does not exist.',
                $value
            );
        }
    }
}
