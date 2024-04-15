<?php

declare(strict_types=1);

namespace Framework;

use ReflectionClass, ReflectionNamedType;
use FrameWork\Exceptions\ContainerException;

/**
 * Class Container
 * 
 * A simple dependency injection container for managing class dependencies.
 */
class Container
{
    // An array to store class definitions
    private array $definitions = [];

    // An arrray to store classes that beeen instantiated
    private array $resolved = [];

    // Adds new class definitions to the container.
    public function addDefinitions(array $newDefinitions)
    {
        // Merge the $newDefintios array with the $defintions array
        $this->definitions = [...$this->definitions, ...$newDefinitions];
    }
    // Resolve
    public function resolve(string $className)
    {
        $reflectionClass = new ReflectionClass($className);
        // Check if instantiable(might be an abstract class, interface or marked with the final keyword)
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class ${className} is not instantiable");
        }


        $constructor = $reflectionClass->getConstructor();
        // Check if the class has a constuctor
        if (!$constructor) {
            return new $className;
        }

        $params = $constructor->getParameters();
        // Check if the class constructor has parameters
        if (count($params) === 0) {
            return new $className;
        }
        // performing validation on the parameters
        $dependencies = [];
        foreach ($params as $param) {
            $name = $param->getName();
            $type = $param->getType();
            // Check if the parameter has type hint
            if (!$type) {
                throw new ContainerException("Failed to resolve class {$className} because param {$name} is missing a type hint.");
            }
            // Check if the type is a class type and  not a built in type
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new ContainerException("Failed to reslve {$className} because invalid param name.");
            }
            $dependencies[] = $this->get($type->getName());
        }
        // Instantiate the class with resolved dependencies.
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    // Retrieves a dependencies from the containber by its identifier
    // Returns the dependency if it exists
    public function get(string $id)
    {
        // Check if the dependecy exists in the container
        if (!array_key_exists($id, $this->definitions)) {
            throw new ContainerException("Class {$id} does not exist in container");
        }

        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        // Retrieve the function that creates an instance of the class
        $factory = $this->definitions[$id];
        $dependency = $factory();
        $this->resolved[$id] = $dependency;
        return $dependency;
    }
}
