<?php

declare(strict_types=1);

namespace App\DI;

use App\Exceptions\Logic\InvalidArgument;
use Nette\DI\Container;

use function array_key_exists;
use function array_keys;
use function get_class;
use function is_a;

abstract class ServiceManager
{
    protected Container $container;
    /** @var array<int|string, string> */
    private array $serviceMap;

    /**
     * @param  array<int|string, string>  $serviceMap
     */
    public function __construct(array $serviceMap, Container $container) {
        $this->serviceMap = $serviceMap;
        $this->container = $container;
    }

    /**
     * @param  int|string  $key
     */
    protected function hasService($key): bool {
        return array_key_exists($key, $this->serviceMap);
    }

    /**
     * @template T of object
     * @param  int|string  $key
     * @param  class-string<T>  $type
     * @return T|null
     */
    protected function getTypedService($key, string $type): ?object {
        $service = $this->getService($key);

        if ($service === null) {
            return null;
        }

        if (!is_a($service, $type)) {
            $this->throwInvalidServiceType($key, $type, $service);
        }

        return $service;
    }

    /**
     * @param  int|string  $key
     */
    protected function getService($key): ?object {
        $serviceName = $this->serviceMap[$key] ?? null;
        if ($serviceName === null) {
            return null;
        }

        return $this->container->getService($serviceName);
    }

    /**
     * @param  int|string  $key
     * @param  class-string  $expectedType
     * @return never
     */
    protected function throwInvalidServiceType($key, string $expectedType, object $service): void {
        $serviceClass = get_class($service);
        $serviceName = $this->getServiceName($key);
        $selfClass = static::class;
        $className = $selfClass;

        throw new InvalidArgument(
            "$selfClass supports only instances of $expectedType. Service '$serviceName' returns instance of $serviceClass. Remove service from $className or make the service return supported object type."
        );
    }

    /**
     * @param  int|string  $key
     */
    protected function getServiceName($key): string {
        if (!isset($this->serviceMap[$key])) {
            $class = static::class;
            $function = __FUNCTION__;

            throw new InvalidArgument(
                "Given key '$key' has no service associated. Trying to call $class->$function(). Call it only with key which exists in service map."
            );
        }

        return $this->serviceMap[$key];
    }

    /**
     * @template T of object
     * @param  int|string  $key
     * @param  class-string<T>  $type
     * @return T
     */
    protected function getTypedServiceOrThrow($key, string $type): object {
        $service = $this->getService($key);

        if ($service === null) {
            $this->throwMissingService($key, $type);
        }

        if (!is_a($service, $type)) {
            $this->throwInvalidServiceType($key, $type, $service);
        }

        return $service;
    }

    /**
     * @param  int|string  $key
     * @param  class-string  $expectedType
     * @return never
     */
    protected function throwMissingService($key, string $expectedType): void {
        $selfClass = static::class;
        $className = $selfClass;

        throw new InvalidArgument(
            "No service is registered under that key but service of type $expectedType is required. Trying to get service by key '$key' from $selfClass. Add service with key '$key' to $className."
        );
    }

    /**
     * @return array<int, int|string>
     */
    protected function getKeys(): array {
        return array_keys($this->serviceMap);
    }
}
