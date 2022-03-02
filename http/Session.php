<?php

namespace common\http;

\session_start();

class Session
{
    const FLASH_KEY = 'flash';

    public function get(string $name): mixed
    {
        return $_SESSION[$name] ?? null;
    }

    public function set(string $name, mixed $data): void
    {
        $_SESSION[$name] = $data;
    }

    public function has(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    public function del(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * @param mixed $flash
     * @return mixed
     */
    public function flash(mixed $flash): mixed
    {
        if ($this->has($this::FLASH_KEY)) {
            $flash = $this->get($this::FLASH_KEY);
            $this->del($this::FLASH_KEY);
            return $flash;
        } else {
            $this->set($this::FLASH_KEY, $flash);
            return '';
        }
    }
}