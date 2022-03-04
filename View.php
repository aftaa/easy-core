<?php

namespace common;

use app\config\view\Config;
use common\exceptions\NotFound;
use common\http\Response;
use common\routes\ViewLayoutTrait;

class View
{
    use ViewLayoutTrait;

    private string $layout;
    private string $output;
    private ?\Throwable $e = null;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->layout = $config->layout;
    }

    public function requireBuffered(string $filename, array $params = [])
    {
        try {
            ob_start();
            extract($params);
            require_once $filename;
            echo file_get_contents($filename);
            $this->output =  ob_get_clean();
        } catch (\Throwable $e) {
            ob_clean();
            $this->e = $e;
        }
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
//        require_once "app/views/$fileName.php";
        $this->requireBuffered("app/views/$fileName.php", $params);
        return $this->output;

//        if ($this->e instanceof NotFound) {
            var_dump($this->e);
            die;
//        }

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
