<?php
declare(strict_types=1);

namespace App\Request;

use Lsr\Core\Models\Model;
use ReflectionClass;
use ReflectionProperty;

/**
 * @template T of Model
 */
abstract class UpdateRequest
{
    /**
     * @param  T  $entity
     */
    public function __construct(
      public readonly Model $entity,
    ) {}

    /**
     * Apply changes to updated entity
     *
     * @post Updates the UpdateRequest::$entity
     *
     * @return T
     */
    public function apply() : Model {
        foreach ($this->getChanges() as $property => $value) {
            $this->entity->$property = $value;
        }
        return $this->entity;
    }

    /**
     * @return array<string, mixed>
     */
    public function getChanges() : array {
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
}