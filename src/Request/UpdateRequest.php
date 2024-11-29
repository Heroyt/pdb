<?php

declare(strict_types=1);

namespace App\Request;

use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Models\Model;
use Lsr\Core\Requests\Request;
use ReflectionClass;
use ReflectionProperty;

/**
 * @template T of Model
 * @phpstan-consistent-constructor
 */
abstract class UpdateRequest
{
    use RequestValidation;
    use RequestPropertySet;

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
            $self->setValidatedValue($value, $property);
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
            $self->setValidatedValue($value, $property);
        }

        return $self;
    }
}
