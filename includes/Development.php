<?php

namespace SpringDevs\WcEssentialAddons;

use Kint\Kint;
use Kint\Renderer\RichRenderer;

/**
 * The development class
 */
class Development
{

    /**
     * Initialize the class
     */
    public static function dd()
    {
        $data = \func_get_args();
        RichRenderer::$theme = 'solarized.css';
        RichRenderer::$folder = false;
        d($data);
    }
}
