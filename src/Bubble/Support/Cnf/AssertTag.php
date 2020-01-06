<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use Traversable;

/**
 * @ingroup cnf
 *
 * @brief The AssertTag class is a derived class of the Literal class that
 * implements literals that check whether applied-to posts have a certain tag.
 *
 * Please see the Literal class for more information about definition and
 * usage.
 */
final class AssertTag
    extends Literal
{
    public string $tag;

    public function __construct(bool $invert, string $tag)
    {
        parent::__construct($invert);
        $this->tag = $tag;
    }

    /**
     * @param array<?string> $parameters
     * @return Traversable<string>
     */
    protected function to_sql_tokens_basic(string $posts, &$parameters)
    {
        $parameters[] = $this->tag;
        yield '(';
        yield "$posts.tags";
        yield '@>';
        yield 'ARRAY';
        yield '[';
        yield 'CAST';
        yield '(';
        yield '$' . (string)\count($parameters);
        yield 'AS';
        yield 'VARCHAR';
        yield ')';
        yield ']';
        yield ')';
    }
}
