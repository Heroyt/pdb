<?php

declare(strict_types=1);

namespace App\Request;

use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Models\Attributes\Validation\Required;
use Lsr\Core\Models\Attributes\Validation\Validator;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

trait RequestValidation
{
    /**
     * @throws ValidationException
     */
    protected static function validateProperty(ReflectionProperty $property, mixed $value): void {
        $propertyName = $property->getName();
        $type = $property->getType();
        assert($type instanceof ReflectionNamedType || $type instanceof ReflectionUnionType);

        if ($value !== null) {
            if ($type instanceof ReflectionUnionType) {
                $types = $type->getTypes();
                foreach ($types as $unionType) {
                    assert($unionType instanceof ReflectionNamedType);
                    $typeName = $unionType->getName();
                    if (!self::isValidType($typeName, $value)) {
                        throw new ValidationException(
                            "Invalid type for property {$propertyName}. Expected {$typeName}, got " . gettype($value) . "."
                        );
                    }
                }
            } else {
                $typeName = $type->getName();
                if (!self::isValidType($typeName, $value)) {
                    throw new ValidationException(
                        "Invalid type for property {$propertyName}. Expected {$typeName}, got " . gettype($value) . "."
                    );
                }
            }
        } else {
            $required = $property->getAttributes(Required::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($required as $requiredAttribute) {
                /** @var Required $attribute */
                $attribute = $requiredAttribute->newInstance();
                $attribute->throw(static::class, $propertyName);
            }
            return; // Not set or nullable
        }

        // Check all validators
        $attributes = $property->getAttributes(Validator::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($attributes as $attributeReflection) {
            /** @var Validator $attribute */
            $attribute = $attributeReflection->newInstance();
            if ($attribute instanceof Required) {
                continue; // Already checked
            }

            $attribute->validateValue($value, static::class, $propertyName);
        }
    }

    protected static function isValidType(string $typeName, mixed $value): bool {
        return match ($typeName) {
            'int' => is_int($value),
            'float' => is_float($value),
            'string' => is_string($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            'object' => is_object($value),
            default => $value instanceof $typeName, // For class types or interfaces
        };
    }
}
