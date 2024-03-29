<?php

namespace Shortcode\Facades;

use Illuminate\Support\Facades\Facade;

class Shortcode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shortcode';
    }
}