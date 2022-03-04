<?php

namespace common;

use app\config\view\Config;
use common\db\QueryProfiler;
use common\exceptions\InternalServerError;
use common\types\DebugMode;
use common\types\Environment;
use common\exceptions\NotFound;

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
            echo $dependencyInjection->outputActionController($routing)->output();
        } catch (NotFound $e) {
            $view = new View(new Config());
            echo $view->render('errors/404', [
                'exception' => $e,
            ]);
        } catch (\Throwable $e) {
            $view = new View(new Config());
            echo $view->render('errors/500', [
                'exception' => $e,
            ]);
        }

        exit;
    }
}
