<?php

namespace springdevs\custompft;

use springdevs\custompft\Frontend\Actions;
use springdevs\custompft\Frontend\Tabs;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        new Actions;
        new Tabs;
    }
}
