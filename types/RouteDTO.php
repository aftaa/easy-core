<?php

namespace common\types;

class RouteDTO extends DTO
{
    public function __construct(
        public string $controller,
        public string $action,
        public ?string $name = null,
        public ?string $path = null,
    )
    { }
}
