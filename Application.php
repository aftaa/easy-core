<?php

namespace common;

use common\db\QueryProfiler;
use common\types\DebugMode;
use common\types\Environment;

class Application
{
    public static ServiceContainer $serviceContainer;
    public static object $config;
    public static DebugMode $debugMode;
    public static Environment $environment;

    /**
     * @param object $config
     * @param DebugMode $debugMode
     * @param Environment $environment
     */
    public function __construct(object $config, $debugMode = DebugMode::false, $environment = Environment::DEV)
    {
        self::$config = $config;
        self::$debugMode = $debugMode;
        self::$environment = $environment;
        self::$serviceContainer = new ServiceContainer();
        /* it's a joke */
        if (!self::$serviceContainer->added((new \ReflectionObject($this))->getName())) {
            self::$serviceContainer->addObject($this);
        }
    }

    /**
     * @return never
     * @throws \ReflectionException
     */
    public function handle(): never
    {
        $router = new Router();
        if (self::$config->router->simple) {
            $routing = $router->parseURL();
        } else {
            $router->collectRoutes();
            $routing = $router->findControllerActionByPath($_SERVER['REQUEST_URI']);
        }
        self::$serviceContainer->addObject($router);

        $interfaceResolver = new InterfaceResolver(self::$config);
        $dependencyInjection = new DependencyInjection(self::$config, self::$serviceContainer, $interfaceResolver);
        self::$serviceContainer->addObject($dependencyInjection);
        try {
            $dependencyInjection->outputActionController($routing);
        } catch (\Exception|\Error $e) {
            header('HTTP/1.0 404 Not found');
            require_once 'common/404.php';
        }

        if (self::$debugMode == DebugMode::true) {
        }
        $this->debug(self::$serviceContainer);
        exit;
    }

    public function debug(ServiceContainer $container)
    {
        if (self::$debugMode == debugMode::false) return;

        if (self::$config->debug->container) {
            echo '<h1>Container</h1><pre>';
            print_r(self::$serviceContainer->getInstances());
            echo '</pre>';
        }

        if (self::$config->debug->queries) {
            /** @var QueryProfiler $queryProfiler */
            $queryProfiler = $container->getObjectByClassName(\common\db\QueryProfiler::class);
            if ($queryProfiler) {
                echo '<h1>Queries:</h1>';
                foreach ($queryProfiler->getInfo() as $query) {
                    echo $query, '<br><br>';
                }
            }
        }
        if (self::$config->debug->routes) {
            echo '<h1>Routes:</h1><pre>';
            $router = new Router;
            $router->debug();
        }
    }
}
