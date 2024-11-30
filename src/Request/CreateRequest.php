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
 */
abstract class CreateRequest
{
    use RequestValidation;
    use RequestPropertySet;

    /**
     * @param  Request  $request  Request with post body
     * @return static
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): self {
        /** @phpstan-ignore new.static */
        $self = new static();

        $class = new ReflectionClass($self);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $value = $request->getPost($propertyName);
            $self->setValidatedValue($value, $property);
        }

        return $self;
    }
}
