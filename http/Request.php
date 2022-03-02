<?php

namespace common\http;

class Request
{
    /**
     * @param string $name
     * @return string
     */
    public function query(string $name): mixed
    {
        return $_GET[$name] ?? null;
    }

    /**
     * @param string $name
     * @return string
     */
    public function post(string $name): mixed
    {
        return $_POST[$name] ?? null;
    }

    /**
     * @param string $name
     * @return string
     */
    public function request(string $name): mixed
    {
        return $_REQUEST[$name] ?? null;
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return !empty($_POST);
    }
}