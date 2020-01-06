<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use Traversable;

/**
 * @ingroup cnf
 *
 * @brief The AssertAuthor class is a derived class of the Literal class that
 * implements literals that check whether applied-to posts belong to a certain
 * author.
 *
 * Please see the Literal class for more information about definition and
 * usage.
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
