<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use Traversable;

/**
 * This CNF literal, when evaluated against a post, checks that the post has
 * or lacks the specified tag as one of its tags.
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
        yield '$' . (string)\count($parameters);
        yield ']';
        yield ')';
    }
}
