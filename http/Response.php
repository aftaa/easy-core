<?php

namespace common\http;

use app\config\view\Config;
use common\Layout;
use common\View;

class Response
{
    const HEADERS = [
        '200' => 'HTTP/1.0 200 OK',
        '404' => 'HTTP/1.0 404 Not Found',
        '500' => 'HTTP/1.0 500 Internal Server Error',
    ];

    private string $output = '';
    private int $httpCode = 200;

    /**
     * @param string $fileName
     * @param array $params
     * @return $this
     * @throws \Throwable
     */
    public function makeRender(string $fileName, array $params = []): self
    {
        $view = new View(new Config());
        $scriptOutput = $view->render($fileName, $params);

        $layoutConfig = new \app\config\layout\Config();
        if ($layoutConfig->enabled && $layoutConfig->layoutEnabled($fileName)) {
            $layout = new Layout($layoutConfig);
            $layoutOutput = $layout->render($scriptOutput);
            $this->output = $layoutOutput;
        } else {
            $this->output = $scriptOutput;
        }
        return $this;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): static
    {
        $this->httpCode = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function output(): string
    {
        header(self::HEADERS[$this->httpCode]);
        return $this->output;
    }
}
