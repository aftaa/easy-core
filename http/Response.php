<?php

namespace common\http;

use common\View;

class Response
{
    public function __construct(
        private string $output,
        private array  $headers,
    )
    {
    }

    /**
     * @return string
     */
    public function output(): string
    {
        foreach ($this->headers as $header) {
            header($header);
        }
        return $this->output;
    }
}