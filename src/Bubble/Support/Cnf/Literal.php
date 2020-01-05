<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use InvalidArgumentException;
use Traversable;

/**
 * A CNF literal is a leaf in the CNF AST. CNF literals are contained within
 * CNF disjunctions. A CNF literal can be evaluated against a post, resulting
 * in a Boolean.
 */
abstract class Literal
{
    /**
     * Whether the predicate is to be inverted, i.e. have the logical NOT
     * operator applied to it.
     */
    public bool $invert;

    protected function __construct(bool $invert)
    {
        $this->invert = $invert;
    }

    /**
     * Create a literal from the columns of a row from the bubble_literals
     * table.
     *
     * Exactly one of the arguments must be non-null, just like in the
     * bubble_literals_predicate_ck constraint.
     */
    public static function from_row(bool $invert,
                                    ?string $assert_author_id,
                                    ?string $assert_tag): Literal
    {
        if ($assert_author_id !== NULL)
            return new AssertAuthor($invert, $assert_author_id);

        if ($assert_tag !== NULL)
            return new AssertTag($invert, $assert_tag);

        throw new InvalidArgumentException('Both arguments are NULL');
    }

    /**
     * Compile the literal into a SQL expression. For details on how this
     * works, see the documentation on the similarly named method in the Cnf
     * class.
     *
     * @param array<?string> $parameters
     * @return Traversable<string>
     */
    public final function to_sql_tokens(string $posts, &$parameters)
    {
        yield '(';
        if ($this->invert)
            yield 'NOT';
        yield from $this->to_sql_tokens_basic($posts, $parameters);
        yield ')';
    }

    /**
     * Like to_sql, but ignore $this->invert.
     *
     * @param array<?string> $parameters
     * @return iterable<string>
     */
    protected abstract function to_sql_tokens_basic(string $posts, &$parameters);
}
