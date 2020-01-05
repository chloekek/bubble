<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

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
}
