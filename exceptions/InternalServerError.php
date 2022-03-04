<?php

namespace common\exceptions;

use app\config\view\Config;
use common\http\Response;
use common\View;

class InternalServerError extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct(?string $message)
    {
        if (null !== $message) {
            parent::__construct($message);
        }
    }


    /**
     * @return $this
     */
    public function setHeader(): self
    {
        header('HTTP/1.0 500 Internal Server Error');
        return $this;
    }

    /**
     * @return Response
     * @throws \Throwable
     */
    public function render()
    {
        echo $this->getMessage();
    }
}
