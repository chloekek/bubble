<?php
declare(strict_types = 1);
namespace Bubble\Support\Web\Widget;

use Bubble\Support\Web\Escape as E;
use Bubble\Support\Web\Widget;

/**
 * HTML element widget.
 *
 * Special characters such as & in attributes
 * are automatically escaped when rendering HTML.
 */
final class Element
    implements Widget
{
    public string $element;

    /** @var array<string,string> */
    public $attributes;

    /** @var Widget[] */
    public $children;

    /**
     * @param array<string,string> $attributes
     * @param Widget[] $children
     */
    public function __construct(string $element, $attributes, $children)
    {
        $this->element = $element;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function toHtml(): void
    {
        // Start tag.
        echo '<';
        echo $this->element;
        foreach ($this->attributes as $key => $value) {
            echo ' ';
            echo $key;
            echo '="';
            echo E::h($value);
            echo '"';
        }
        echo '>';

        // Children.
        foreach ($this->children as $child)
            $child->toHtml();

        // End tag.
        echo '</';
        echo $this->element;
        echo '>';
    }
}
