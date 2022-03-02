<?php

namespace common\routes;

class FileNameToClassName
{
    /**
     * @param string $filename
     * @return string
     */
    public function transform(string $filename): string
    {
//        $filename = str_replace('app/controllers/', '', $filename);
        $filename = str_replace('/', '\\', $filename);
        $filename = str_replace('.php', '', $filename);
        return $filename;
    }
}