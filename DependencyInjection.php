<?php

namespace common;

use common\exceptions\InternalServerError;
use common\http\Response;
use common\types\DebugMode;
use common\types\RouteDTO;
use common\exceptions\NotFound;

class DependencyInjection
{
    private object $config;
    private ServiceContainer $serviceContainer;
    private InterfaceResolver $interfaceResolver;
    private array $loadedClasses = [];

    public function __construct(object $config, ServiceContainer $container, InterfaceResolver $interfaceResolver)
    {
        $this->config = $config;
        $this->serviceContainer = $container;
        $this->interfaceResolver = $interfaceResolver;
    }

    /**
     * @param RouteDTO|null $routing
     * @return Response
     * @throws InternalServerError
     * @throws NotFound
     */
    public function outputActionController(?RouteDTO $routing): Response
    {
        try {
            if (null === $routing) {
                throw new NotFound();
            }

            $controller = $this->makeDependencyInjection($routing->controller);
            $controllerReflection = new \ReflectionObject($controller);

            $parameters = $controllerReflection->getMethod($routing->action)->getParameters();
            $arguments = [];
            foreach ($parameters as $parameter) {
                $type = $parameter->getType()->getName();
                $arguments[] = $this->makeDependencyInjection($type);
            }


            $response = $controller->{$routing->action}(...$arguments);
            if (!$response) {
                throw new InternalServerError("Missing the response object");
            }
            return $response;
        } catch (\ReflectionException $e) {
            throw new NotFound();
        }
    }

    /**
     * @param string $name
     * @return object
     * @throws \ReflectionException
     */
    public function makeDependencyInjection(string $name): object
    {
        $classReflection = new \ReflectionClass($name);

        /* container checks */
        $className = $classReflection->getName();
        if ($this->serviceContainer->added($className) && $this->config->container->enabled) {
            if (!in_array($className, $this->config->container->disabled)) {
                return $this->serviceContainer->getObjectByClassName($className);
            }
        }

        if ($classReflection->isInterface()) {
            [$classReflection, $name] = $this->interfaceResolver->resolve($name, $classReflection);
        }

        if (Application::$debugMode == DebugMode::true && $this->config->debug->load_class) {
            echo 'load class: ' . $classReflection->getName(), '<br>';
        }
        $this->loadedClasses[] = $classReflection->getName();

        $parameters = $this->getConstructorParameters($classReflection);//$constructor->getParameters();
        $arguments = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType()->getName();
            if (preg_match('/float|int|string|bool/', $type)) {
                $arguments[] = $parameter;
            } else {
                $arguments[] = $this->makeDependencyInjection($type);
            }
        }
        //$object = new $name(...$arguments);
        $object = $classReflection->newInstance(...$arguments);
        if (!in_array($className, $this->config->container->disabled)) {
            $this->serviceContainer->addObject($object);
        }
        return $object;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getConstructorParameters(\ReflectionClass $reflectionClass): array
    {
        $parameters = $reflectionClass->getConstructor() ? $reflectionClass->getConstructor()->getParameters() : [];
        if ($reflectionClass->getParentClass()) {
            $parameters = array_merge($parameters, $this->getConstructorParameters($reflectionClass->getParentClass()));
        }
        return $parameters;
    }

    /**
     * @return array
     */
    public function getLoadedClasses(): array
    {
        return $this->loadedClasses;
    }

    public function addLoadedClass(string $className)
    {
        $this->loadedClasses[] = $className;
    }
}
