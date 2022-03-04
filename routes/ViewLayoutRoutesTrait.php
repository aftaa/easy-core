<?php

namespace common\routes;

use common\Application;
use common\Router;
use common\types\RouteDTO;

trait ViewLayoutRoutesTrait
{
    /**
     * @param string $name
     * @param array $params
     * @return string|null
     * @throws \Exception
     */
    public function route(string $name, array $params = []): ?string
    {
        /** @var Router $router */
        $router = Application::$serviceContainer->get(Router::class);
        return $router->route($name, $params);
    }

    /**
     * @return RouteDTO
     */
    public function getActiveRoute(): RouteDTO
    {
        /** @var Router $router */
        $router = Application::$serviceContainer->get(Router::class);
        return $router->getActiveRoute();
    }
}
