<?php
declare(strict_types = 1);
namespace Bubble\Support\Web;

use Bubble\Support\Web\Escape as E;

/**
 * Convenient functions for echoing HTML.
 *
 * The reason we use these functions instead of directly echoing HTML is
 * twofold. In the first place, it is too easy to forget to escape special
 * characters, introducing a security hazard. In the second place, formatting
 * the code nicely is difficult due to how HTML treats whitespace. You are
 * probably familiar with the classic “trailing underline” problem in
 * hyperlinks formatted like in figure 1. This happens because there is space
 * between the text and the closing tag. Eliminating this whitespace results in
 * awkward formatting such as figure 2 and figure 3. Compare with figure 4,
 * which uses this class and suffers from neither issue.
 *
 * Figure 1:
 *
 *     <a href="/">
 *         Home
 *     </a>
 *
 * Figure 2:
 *
 *     <a href="/">
 *         Home</a>
 *
 * Figure 3:
 *
 *     <a href="/">
 *         Home<!--
 *     --></a>
 *
 * Figure 4:
 *
 *     H::open('a', [ 'href' => '/' ]);
 *         H::text('Home');
 *     H::close('a');
 */
final class Html
{
    private function __construct()
    {
    }

    /**
     * Echo an opening tag with the given element name and attributes.
     * Special characters in attribute values are automatically escaped.
     *
     * @param array<string,string> $attributes
     */
    public static function open(string $element, $attributes): void
    {
        echo '<';
        echo $element;
        foreach ($attributes as $key => $value) {
            echo ' ';
            echo $key;
            echo '="';
            echo E::h($value);
            echo '"';
        }
        echo '>';
    }

    /**
     * Echo a closing tag.
     */
    public static function close(string $element): void
    {
        echo '</';
        echo $element;
        echo '>';
    }

    /**
     * Echo a text node.
     *
     * Special characters in the text are automatically escaped.
     */
    public static function text(string $text): void
    {
        echo E::h($text);
    }
}
