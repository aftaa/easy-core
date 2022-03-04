<?php

namespace common\exceptions;

class NotFound extends \Exception
{
    /**
     * @return void
     */
    public function setHeader(): void
    {
        header('HTTP/1.0 404 Not Found');
    }

    public function render()
    {
        echo $this->getMessage();
    }
}
