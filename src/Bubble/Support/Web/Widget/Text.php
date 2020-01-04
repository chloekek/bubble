<?php
declare(strict_types = 1);
namespace Bubble\Support\Web\Widget;

use Bubble\Support\Web\Escape as E;
use Bubble\Support\Web\Widget;

/**
 * HTML text node widget.
 *
 * Special characters such as & in the text
 * are automatically escaped when rendering HTML.
 */
final class Text
    implements Widget
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toHtml(): void
    {
        echo E::h($this->text);
    }
}
