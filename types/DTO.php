<?php

namespace common\types;

use ErrorException;

class DTO
{
    /**
     * @param string $name
     * @param string $value
     * @throws ErrorException
     */
    final public function __set(string $name, string $value)
    {
        $className = static::class;
        throw new ErrorException("Undefined property $className::\$$name");
    }

    /**
     * @param string $name
     * @throws ErrorException
     */
    final public function __get(string $name)
    {
        $className = static::class;
        throw new ErrorException("Undefined property $className::\$$name");
    }
}
