<?php

namespace common;

use app\config\view\Config;
use common\http\Response;

class Controller
{
    /**
     * @param $name
     * @param array $params
     * @return never
     */
    public function toRoute($name, array $params = []): never
    {
        /** @var Router router */
        $router = Application::$serviceContainer->getObjectByClassName(Router::class);
        $path = $router->findPathByName($name, $params);
        $params = http_build_query($params);
        if ($params) {
            $path = '?' .  $path;
        }
        header("Location: $path");
        exit;
    }

    /**
     * @param string $fileName
     * @param array $params
     * @return Response
     * @throws \Throwable
     */
    protected function render(string $fileName, array $params = []): Response
    {
        return (new Response())->makeRender($fileName, $params)->setCode(200);
    }

    /**
     * @param string $string
     * @return never
     */
    protected function redirect(string $string): never
    {
        header("Location: $string");
        exit();
    }
}
