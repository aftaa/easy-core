<?php

namespace exceptions;

class NotFound extends \Exception
{
    public function setHeader()
    {
        header('HTTP/1.0 404 Not Found');
    }
}
