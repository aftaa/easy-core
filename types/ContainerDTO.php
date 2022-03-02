<?php

namespace common\types;

class ContainerDTO extends DTO
{
    /**
     * @param object $object
     * @param bool $isCached
     */
    public function __construct(
        public object $object,
        public bool $isCached)
    { }
}
