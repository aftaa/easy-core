<?php

namespace common;

use app\config\view\Config;
use common\http\Response;
use common\routes\ViewLayoutTrait;

class View
{
    use ViewLayoutTrait;

    public ?string $layout = 'app/layouts/layout.php';

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->layout = $config->layout;
    }

    /**
     * @param string $fileName
     * @param array $params
     * @return string
     */
    public function render(string $fileName, array $params = []): Response
    {
        ob_start();
        extract($params);
        require_once "app/views/$fileName.php";

        $headers = [
            'HTTP/1.0 200 OK',
            'Content-type: text/html; charset=UTF-8',
        ];

        if (null === $this->layout) {
            $response = new Response(ob_get_clean(), $headers);
        } else {
            $layout = new Layout(ob_get_clean());
            $params = [];
            foreach ($this as $varName => $varValue) {
                $params[$varName] = $varValue;
            }
            $response = new Response($layout->render(layoutFileName: $this->layout, params: $params), $headers);
        }
        return $response;
    }
}
