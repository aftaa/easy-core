<?php

namespace common;

use common\routes\FileNameToClassName;
use common\routes\Route;
use common\types\RouteDTO;

class Router
{
    const DEFAULT_INDEX = 'index';
    const ACTION_SUFFIX = 'Action';
    const CONTROLLER_SUFFIX = 'Controller';
    const CONTROLLERS_NAMESPACE = 'app\\controllers\\';

    /** @var array|RouteDTO[] */
    private array $byName = [];
    /** @var array|RouteDTO[] */
    private array $byPath = [];

    private RouteDTO $activeRoute;

    function parseURL(): RouteDTO
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = preg_replace('{//+}', '/', $requestUri);
        preg_match('{/([a-z0-9_]+)?/?([a-z0-9_]+)?}i', $requestUri, $matches);
        $controllerName = $matches[1] ?? self::DEFAULT_INDEX;
        $actionName = $matches[2] ?? self::DEFAULT_INDEX;
        $controllerName = self::CONTROLLERS_NAMESPACE . ucfirst($controllerName) . self::CONTROLLER_SUFFIX;
        $actionName .= self::ACTION_SUFFIX;

        return new RouteDTO($controllerName, $actionName);
    }

    /**
     * @param string $folder
     * @return array
     */
    public function collectFiles(string $folder = 'app/controllers'): array
    {
        static $paths = null;
        foreach (glob("$folder/*.php") as $php) {
            $paths[] = $php;
        }
        $folders = glob("$folder/*", GLOB_ONLYDIR);
        foreach ($folders as $folder) {
            $this->collectFiles($folder);
        }
        return $paths;
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function collectRoutes(): void
    {
        $files = $this->collectFiles();
        $fileNameToClassName = new FileNameToClassName();
        foreach ($files as $file) {
            $class = $fileNameToClassName->transform($file);
            $reflection = new \ReflectionClass($class);

            $pathPrefix = '';
            $attributes = $reflection->getAttributes(Route::class);
            foreach ($attributes as $attribute) {
                $arguments = $attribute->getArguments();
                $path = $arguments[0];
                $pathPrefix = $path;
            }

            foreach ($reflection->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    $arguments = $attribute->getArguments();
                    $name = @$arguments['name'];
                    $path = $arguments[0];
                    if ($pathPrefix) {
                        $path = $pathPrefix . $path;
                    }
                    $routeDTO = new RouteDTO($class, $method->name, $name, $path);
                    if ($name) {
                        $this->byName[$name] = $routeDTO;
                    }
                    $this->byPath[$path] = $routeDTO;
                }
            }
        }
    }

    /**
     * @param string $path
     * @return RouteDTO|null
     */
    public function findControllerActionByPath(string $path): ?RouteDTO
    {
        $path = parse_url($path, PHP_URL_PATH);
        if (isset($this->byPath[$path])) {
            $this->activeRoute = $this->byPath[$path];
            return $this->byPath[$path];
        }
        return null;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function findPathByName(string $name): ?string
    {
        if (isset($this->byName[$name])) {
            return $this->byName[$name]->path;
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function route(string $name, array $params = []): string
    {
        $params = http_build_query($params);
        $path = $this->findPathByName($name);
        if ($params) {
            $path = "$path?$params";
        }
        if (null === $path) {
            throw new \Exception('">Не найден маршрут ' . $name);
        }
        return $path;
    }

    /**
     * @return RouteDTO
     */
    public function getActiveRoute(): RouteDTO
    {
        return $this->activeRoute;
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function debug(): void
    {
        $this->collectRoutes();
        /** @var $byPath RouteDTO[] */
        $byPath = $this->byPath;
        ?>
        <table border="1">
            <thead>
            <tr>
                <th>Name</th>
                <th>Path</th>
                <th>Controller</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($byPath as $path => $routing): ?>
                <tr>
                    <td><?= $routing->name ?></td>
                    <td><?= $routing->path ?></td>
                    <td><?= $routing->controller ?></td>
                    <td><?= $routing->action ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php
    }
}
