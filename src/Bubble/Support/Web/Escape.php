<?php
declare(strict_types = 1);
namespace Bubble\Support\Web;

/**
 * Routines for escaping text for inclusion in web-related data formats.
 */
final class Escape
{
    private function __construct()
    {
    }

    /**
     * The composition self::h ∘ self::u.
     */
    public static function hu(string $s): string
    {
        // \urlencode actually never returns characters that self::h would
        // escape, so we do not need to actually call self::h.
        // TODO: Verify this property with a generative test.
        return \urlencode($s);
    }

    /**
     * Escape a string for inclusion in an URL.
     */
    public static function u(string $s): string
    {
        return \urlencode($s);
    }

    /**
     * Escape a string for inclusion as text in HTML.
     */
    public static function h(string $s): string
    {
        $f = \ENT_HTML5 | \ENT_QUOTES;
        return \htmlspecialchars($s, $f);
    }
}
