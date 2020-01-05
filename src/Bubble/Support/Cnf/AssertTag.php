<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

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
}
