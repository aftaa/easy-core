<?php

namespace common;

use app\config\view\Config;
use common\routes\ViewLayoutRoutesTrait;

class View
{
    use ViewLayoutRoutesTrait;

    /**
     * @param Config $config
     */
    public function __construct(
        private Config $config,
    )
    { }

    /**
     * @param string $filename
     * @param array $params
     * @param $finalHandler
     * @return string|false
     */
    public function render(string $filename, array $params = []): string|false
    {
        try {
            $filename = "{$this->config->viewPath}/$filename.php";
            ob_start();
            extract($params);
            include $filename;
            return ob_get_clean();
        } catch (\Throwable $e) {
            ob_clean();
            throw $e;
        }
    }
}
