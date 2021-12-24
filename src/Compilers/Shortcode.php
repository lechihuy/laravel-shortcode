<?php

namespace Shortcode\Compilers;

use Illuminate\View\ComponentAttributeBag;

class Shortcode
{
    /**
     * The shortcode tag name.
     *
     * @var string
     */
    public $name;

    /**
     * The shortcode content.
     *
     * @var string
     */
    public $content;

    /**
     * The shortcode attribute bag.
     * 
     * @var ComponentAttributeBag
     */
    public $attributes;

    /**
     * The shortcode raw attributes string.
     * 
     * @var string
     */
    public $rawAttributes;

    /**
     * Create a new shortcode instance.
     *
     * @param  string  $name
     * @param  string  $content
     * @param  array   $attributes
     * @return void
     */
    public function __construct($name, $content, $attributes = [], $rawAttributes = null)
    {
        $this->name = $name;
        $this->content = $content;
        $this->attributes = new ComponentAttributeBag($attributes);
        $this->rawAttributes = $rawAttributes;
    }
}