<?php

namespace Shortcode;

use Shortcode\Shortcode;
use Shortcode\Compilers\ShortcodeCompiler;
use Illuminate\Support\ServiceProvider;

class ShortcodeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCompiler();
        $this->registerShortcode();
    }

    /**
     * Register the shortcode compiler.
     * 
     * @return void
     */
    public function registerCompiler()
    {
        $this->app->singleton('shortcode.compiler', function ($app) {
            return new ShortcodeCompiler();
        });
    }

    /**
     * Register the shortcode.
     * 
     * @return void
     */
    public function registerShortcode()
    {
        $this->app->singleton('shortcode', function ($app) {
            return new Shortcode($app['shortcode.compiler']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
