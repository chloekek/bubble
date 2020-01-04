<?php
declare(strict_types = 1);
namespace Bubble\Support\Web\Widget;

use Bubble\Support\Web\Widget;

/**
 * Convenient functions for constructing widgets.
 */
final class Html
{
    private function __construct()
    {
    }

    public static function text(string $text): Text
    { return new Text($text); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function a($attributes, ...$children): Element
    { return new Element('a', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function article($attributes, ...$children): Element
    { return new Element('article', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function button($attributes, ...$children): Element
    { return new Element('button', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function form($attributes, ...$children): Element
    { return new Element('form', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function nav($attributes, ...$children): Element
    { return new Element('nav', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function section($attributes, ...$children): Element
    { return new Element('section', $attributes, $children); }

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public static function textarea($attributes, ...$children): Element
    { return new Element('textarea', $attributes, $children); }
}
