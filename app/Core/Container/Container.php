<?php

namespace LightMVC\Core\Container;

use Closure;
use LightMVC\Core\Container\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionParameter;

class Container
{
    private static $instance = null;
    private $bindings = [];
    private $instances = [];
    private $aliases = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            static::$instance = new static();
        }
        return self::$instance;
    }

    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function make(string $abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        // Возвращаем существующий экземпляр для синглтонов
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        $object = $this->build($concrete, $parameters);

        // Сохраняем экземпляр, если это синглтон
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function getClosure(string $abstract, string $concrete): Closure
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract === $concrete) {
                return $container->build($concrete, $parameters);
            }

            return $container->resolve($concrete, $parameters);
        };
    }

    private function build($concrete, array $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete();
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters(), $parameters);

        return $reflector->newInstanceArgs($dependencies);
    }


    private function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // Если параметр передан явно, используем его
            if (isset($parameters[$dependency->getName()])) {
                $results[] = $parameters[$dependency->getName()];
                continue;
            }

            // Если есть тип параметра, пытаемся его разрешить
            if ($type = $dependency->getType()) {
                $results[] = $this->resolveByType($dependency);
                continue;
            }

            // Если есть значение по умолчанию, используем его
            if ($dependency->isDefaultValueAvailable()) {
                $results[] = $dependency->getDefaultValue();
                continue;
            }

            var_dump($dependency);
            exit();
            throw new ContainerException("Unable to resolve dependency: {$dependency->getName()}");
        }

        return $results;
    }

    private function resolveByType(ReflectionParameter $dependency)
    {
        $type = $dependency->getType();

        if (!$type->isBuiltin()) {
            return $this->make($type->getName());
        }

        return $dependency->getDefaultValue();
    }

    private function getConcrete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    private function isShared(string $abstract): bool
    {
        return isset($this->bindings[$abstract]['shared']) &&
            $this->bindings[$abstract]['shared'] === true;
    }

    private function getAlias(string $abstract): string
    {
        return isset($this->aliases[$abstract])
            ? $this->getAlias($this->aliases[$abstract])
            : $abstract;
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) ||
            isset($this->instances[$abstract]) ||
            isset($this->aliases[$abstract]);
    }
}
