<?php
declare(strict_types = 1);
namespace Bubble\Support\Web;

/**
 * A widget is anything that can be rendered to HTML.
 * Some widgets allow the embedding of other widgets.
 *
 * The reason we use widgets instead of directly printing HTML
 * is that the latter is prone to accidental omission of escaping code
 * and results in a lot of redundant whitespace which complicates layouting.
 */
interface Widget
{
    /**
     * Render the widget to HTML.
     */
    function toHtml(): void;
}
