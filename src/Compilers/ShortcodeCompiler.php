<?php

namespace Shortcode\Compilers;

use Illuminate\Support\Str;

class ShortcodeCompiler
{
    /**
     * The index of shortcode raw string in the matches.
     * 
     * @var int
     */
    const SHORTCODE_RAW_STRING_INDEX = 0;

    /**
     * The index of shortcode tagname in the matches.
     * 
     * @var int
     */
    const SHORTCODE_TAGNAME_INDEX = 2;

    /**
     * The index of shortcode raw attributes in the matches.
     * 
     * @var int
     */
    const SHORTCODE_RAW_ATTRIBUTES_INDEX = 3;

    /**
     * The index of shortcode raw content in the matches.
     * 
     * @var int
     */
    const SHORTCODE_RAW_CONTENT_INDEX = 5;

    /**
     * All the registerd shortcodes.
     * 
     * @var array
     */
    protected $shortcodes = [];

    /**
     * Add a new shortcode.
     * 
     * @param  string  $name
     * @param  callable|string  $callback
     * @return void
     */
    public function addShortCode($name, $callback)
    {
        $this->shortcodes[$name] = $callback; 
    }

    /**
     * Complie the given content.
     * 
     * @param  string  $value
     * @return string
     */
    public function compile($content)
    {
        if (!Str::contains($content, '['))
            return $content;

        if (count($this->shortcodes) === 0)
            return $content;

        return $this->renderShortcodes($content);
    }

    /**
     * Render the shortcodes to HTML format.
     * 
     * @param  string  $content
     * @return string
     */
    protected function renderShortcodes($content)
    {
        $pattern = $this->getShortcodesRegex();

        return preg_replace_callback("/{$pattern}/s", [$this, 'renderShortcode'], $content);
    }

    /**
     * Render the shortcode to HTML format.
     * 
     * @param  array  $matches
     * @return string
     */
    public function renderShortcode($matches)
    {
        $shortcode = $this->makeShortcode($matches);
        $name = $shortcode->name;

        return call_user_func_array($this->shortcodes[$name], [
            $shortcode,
            $this,
        ]);
    }

    /**
     * Get all shortcode tags that were registered for the compiler.
     * 
     * @return array
     * @author Wordpress
     */
    protected function getShortcodeTags()
    {
        return array_map('preg_quote', array_keys($this->shortcodes));
    }

    /**
     * Get the regex that used to get shortcodes in the content.
     * 
     * @return string 
     * @author Wordpress
     */
    protected function getShortcodesRegex()
    {
        $shortcodeTags = $this->getShortcodeTags();
        $shortcodeTagsRegex = join('|', $shortcodeTags);

        return '\\['                         // Opening bracket.
        . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
        . "($shortcodeTagsRegex)"            // 2: Shortcode name.
        . '(?![\\w-])'                       // Not followed by word character or hyphen.
        . '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
        .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
        .     '(?:'
        .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
        .         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
        .     ')*?'
        . ')'
        . '(?:'
        .     '(\\/)'                        // 4: Self closing tag...
        .     '\\]'                          // ...and closing bracket.
        . '|'
        .     '\\]'                          // Closing bracket.
        .     '(?:'
        .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
        .             '[^\\[]*+'             // Not an opening bracket.
        .             '(?:'
        .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
        .                 '[^\\[]*+'         // Not an opening bracket.
        .             ')*+'
        .         ')'
        .         '\\[\\/\\2\\]'             // Closing shortcode tag.
        .     ')?'
        . ')'
        . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]].
    }

    /**
     * Create a shortcode instance.
     * 
     * @param  array  $match
     * @return Shortcode
     */
    protected function makeShortcode($matches)
    {
        $rawAttributes = trim($matches[static::SHORTCODE_RAW_ATTRIBUTES_INDEX]);
        $attributes = $this->parseAttributes($rawAttributes);

        return new Shortcode(
            $matches[static::SHORTCODE_TAGNAME_INDEX],
            $this->compile($matches[static::SHORTCODE_RAW_CONTENT_INDEX]),
            $attributes,
            $rawAttributes,
        );
    }

    /**
     * Parse the raw string attribute to array format.
     * 
     * @param  string  $text
     * @return string
     * @author Wordpress
     */
    protected function parseAttributes($text)
    {
        $text = htmlspecialchars_decode($text, ENT_QUOTES);
        $attributes = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        
        if (preg_match_all($pattern, preg_replace('/[\x{00a0}\x{200b}]+/u', " ", $text), $bunchOfmatches, PREG_SET_ORDER)) {
            foreach ($bunchOfmatches as $matches) {
                if (!empty($matches[1])) { // attribute="value"
                    $attributes[strtolower($matches[1])] = stripcslashes($matches[2]);
                } elseif (!empty($matches[3])) { // attribute='value'
                    $attributes[strtolower($matches[3])] = stripcslashes($matches[4]);
                } elseif (!empty($matches[5])) { // attribute=value
                    $attributes[strtolower($matches[5])] = stripcslashes($matches[6]);
                } elseif (isset($matches[8])) { // attribute
                    $attributes[stripcslashes($matches[8])] = true;
                }
            }
        } else {
            $attributes = ltrim($text);
        }

        if (!$attributes) return [];
        
        return is_array($attributes) ? $attributes : [$attributes];
    }
}