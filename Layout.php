<?php

namespace common;

use common\routes\ViewLayoutTrait;

class Layout
{
    use ViewLayoutTrait;

    /**
     * @param string $content
     */
    public function __construct(
        private string $content,
    )
    { }

    /**
     * @param $layoutFileName
     * @param $params
     * @return string
     */
    public function render($layoutFileName, $params): string
    {
        ob_start();
        extract($params);
        require_once "$layoutFileName.php";
        return ob_get_clean();
    }
}
