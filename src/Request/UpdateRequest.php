<?php

declare(strict_types=1);

namespace App\Request;

use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Models\Attributes\Validation\Required;
use Lsr\Core\Models\Attributes\Validation\Validator;
use Lsr\Core\Models\Model;
use Lsr\Core\Requests\Request;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * @template T of Model
 * @phpstan-consistent-constructor
 */
abstract class UpdateRequest
{
    /**
     * @param  T  $entity
     */
    public function __construct(
        public readonly Model $entity,
    ) {
    }

    /**
     * Apply changes to updated entity
     *
     * @post Updates the UpdateRequest::$entity
     *
     * @return T
     */
    public function apply(): Model {
        foreach ($this->getChanges() as $property => $value) {
            $this->entity->$property = $value;
        }
        return $this->entity;
    }

    /**
     * @return array<string, mixed>
     */
    public function getChanges(): array {
        $class = new ReflectionClass($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $changes = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName === 'entity') {
                continue;
            }
            if (
                isset($this->$propertyName)
                && property_exists($this->entity, $propertyName)
                && $this->entity->$propertyName !== $this->$propertyName
            ) {
                $changes[$propertyName] = $this->$propertyName;
            }
        }
        return $changes;
    }

    /**
     * @param  Request  $request  Request with post body
     * @param  T  $entity
     * @return static
     * @throws ValidationException
     */
    public static function fromRequest(Request $request, Model $entity): self {
        $self = new static($entity);

        $class = new ReflectionClass($self);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName === 'entity') {
                continue;
            }

            $value = $request->getPost($propertyName);

            $type = $property->getType();
            assert($type instanceof ReflectionNamedType || $type instanceof ReflectionUnionType);

            if ($value === null) {
                if ($type->allowsNull()) {
                    $self->$propertyName = null;
                }
                continue; // Value not set
            }

            static::validateProperty($property, $value);

            $self->$propertyName = $value;
        }

        return $self;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  T  $entity
     * @return static
     * @throws ValidationException
     */
    public static function fromArray(array $data, Model $entity): self {
        $self = new static($entity);

        $class = new ReflectionClass($self);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName === 'entity') {
                continue;
            }

            if (!array_key_exists($propertyName, $data)) {
                continue;
            }
            $value = $data[$propertyName];

            $type = $property->getType();
            assert($type instanceof ReflectionNamedType || $type instanceof ReflectionUnionType);

            if ($value === null) {
                if ($type->allowsNull()) {
                    $self->$propertyName = null;
                }
                continue; // Value not set
            }

            static::validateProperty($property, $value);

            $self->$propertyName = $value;
        }

        return $self;
    }

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
