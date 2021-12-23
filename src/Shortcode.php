<?php

namespace Shortcode;

use Shortcode\Compilers\ShortcodeCompiler;

class Shortcode
{
    /**
     * The shortcode compiler.
     * 
     * @var Compiler
     */
    protected $compiler;

    /**
     * Constructor.
     * 
     * @param  Compiler  $compiler
     * @return void
     */
    public function __construct(ShortcodeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Register a new shortcode.
     * 
     * @param  string  $name
     * @param  callable|string  $callback
     * @return $this
     */
    public function register($name, $callback)
    {
        $this->compiler->addShortCode($name, $callback);

        return $this;
    }

    /**
     * Complie the given content.
     * 
     * @param  string  $value
     * @return string
     */
    public function compile($value)
    {
        return $this->compiler->compile($value);
    }
}