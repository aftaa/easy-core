<?php

namespace common;

use app\config\layout\Config;
use common\routes\ViewLayoutRoutesTrait;

class Layout
{
    use ViewLayoutRoutesTrait;

    private string $content;

    /**
     * @param Config $config
     */
    public function __construct(
        private Config $config,
    )
    {
    }

    /**
     * @param string $scriptOutput
     * @return string|false
     */
    public function render(string $scriptOutput): string|false
    {
        try {
            $filename = "{$this->config->layoutPath}/{$this->config->layout}.php";
            ob_start();
            $this->content = $scriptOutput;
            include $filename;
            return ob_get_clean();
        } catch (\Throwable $e) {
            ob_clean();
            throw $e;
        }
    }
}
