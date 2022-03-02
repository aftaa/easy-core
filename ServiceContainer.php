<?php

namespace common;

use common\types\ContainerDTO;

class ServiceContainer
{
    private array $instances;

    /**
     * @param string $className
     * @return bool
     */
    public function added(string $className): bool
    {
        return isset($this->instances[$className]);
    }

    /**
     * @param object $object
     * @return void
     */
    public function addObject(object $object)
    {
        $reflection = new \ReflectionObject($object);
        $name = $reflection->getName();
        $this->instances[$name] = $object;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function has(string $className): bool
    {
        return $this->added($className);
    }

    /**
     * @param string $className
     * @return object|false
     */
    public function getObjectByClassName(string $className): object|false
    {
        if ($this->added($className)) {
            return $this->instances[$className];
        }
        return false;
        //throw new \Exception("Service container: <b>$className</b> not found.");
    }

    /**
     * @param string $className
     * @return object|false
     */
    public function get(string $className): object|false
    {
        return $this->getObjectByClassName($className);
    }

    /**
     * @param string $className
     * @return object
     * @throws \ReflectionException
     */
    public function init(string $className): object
    {
        if (!$this->has($className)) {
            /** @var DependencyInjection $dependencyInjection */
            $dependencyInjection = $this->get(DependencyInjection::class);
            $object = $dependencyInjection->makeDependencyInjection($className);
            $this->add($object);
        } else {
            $object = $this->get($className);
        }
        return $object;
    }

    /**
     * @return array
     */
    public function getInstances(): array
    {
        return $this->instances;
    }

    /**
     * @param object $object
     * @return void
     */
    private function add(object $object)
    {
        $this->addObject($object);
    }
}
