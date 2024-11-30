<?php

declare(strict_types=1);

namespace App\Request;

use Lsr\Core\Exceptions\ValidationException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

trait RequestPropertySet
{
    /**
     * @param  mixed  $value
     * @param  ReflectionProperty  $property
     * @return void
     * @throws ValidationException
     */
    protected function setValidatedValue(mixed $value, ReflectionProperty $property): void {
        $propertyName = $property->getName();
        $type = $property->getType();
        assert($type instanceof ReflectionNamedType || $type instanceof ReflectionUnionType);

        if ($value === null) {
            if ($type->allowsNull()) {
                $this->$propertyName = null;
            }
            return; // Value not set
        }

        static::validateProperty($property, $value);

        $this->$propertyName = $value;
    }
}
