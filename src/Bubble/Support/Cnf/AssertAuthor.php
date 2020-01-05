<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use Traversable;

/**
 * This CNF literal, when evaluated against a post, checks that the post
 * belongs to or does not belong to the specified author.
 */
final class AssertAuthor
    extends Literal
{
    public string $author_id;

    public function __construct(bool $invert, string $author_id)
    {
        parent::__construct($invert);
        $this->author_id = $author_id;
    }

    /**
     * @param array<?string> $parameters
     * @return Traversable<string>
     */
    protected function to_sql_tokens_basic(string $posts, &$parameters)
    {
        $parameters[] = $this->author_id;
        yield '(';
        yield "$posts.author_id";
        yield '=';
        yield '$' . (string)\count($parameters);
        yield ')';
    }
}
