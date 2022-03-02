<?php

namespace common;

class InterfaceResolver
{
    private object $config;

    public function __construct(object $config)
    {
        $this->config = $config;
    }

    /**
     * @param mixed $name
     * @param \ReflectionClass $classReflection
     * @return array
     * @throws \ReflectionException
     */
    public function resolve(mixed $name, \ReflectionClass $classReflection): array
    {
        foreach (Application::$config->interfaces as $interface => $realization) {
            if ($name == $interface) {
                $classReflection = new \ReflectionClass($realization);
                $name = $realization;
            }
        }
        return [$classReflection, $name];
    }
}
