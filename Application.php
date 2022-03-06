<?php

namespace common;

use app\config\view\Config;
use common\http\Response;
use common\types\DebugMode;
use common\types\Environment;
use common\exceptions\NotFound;
use common\types\RouteDTO;

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
     * @return RouteDTO|null
     * @throws \ReflectionException
     */
    private function handleRouter(): ?RouteDTO
    {
        $router = new Router();
        if (self::$config->router->simple) {
            $routing = $router->parseURL();
        } else {
            $router->collectRoutes();
            $routing = $router->findControllerActionByPath($_SERVER['REQUEST_URI']);
        }
        self::$serviceContainer->addObject($router);
        return $routing;
    }

    /**
     * @return never
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle(): never
    {
        $interfaceResolver = new InterfaceResolver(self::$config);
        $dependencyInjection = new DependencyInjection(self::$config, self::$serviceContainer, $interfaceResolver);
        self::$serviceContainer->addObject($dependencyInjection);

        $routing = $this->handleRouter();

        try {
            ob_start();
            echo $dependencyInjection->outputActionController($routing)->output();
        } catch (NotFound $e) {
            ob_clean();
            $view = new View(new Config());
            echo $view->render('errors/404', [
                'exception' => $e,
            ]);
        } catch (\Throwable $e) {
            ob_clean();
            echo (new Response())->makeRender('errors/500', [
                'exception' => $e,
            ])->setCode(500)->output();
        }

        exit;
    }
}
