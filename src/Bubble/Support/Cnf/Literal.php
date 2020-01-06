<?php
declare(strict_types = 1);
namespace Bubble\Support\Cnf;

use InvalidArgumentException;
use Traversable;

/**
 * @ingroup cnf
 *
 * @brief The Literal class implements data structures for CNF literals.
 *
 * The Literal class is a base class that is derived from by classes for the
 * different kinds of literals. The Literal class has methods to turn database
 * rows into CNF literals, as well as methods to compile CNF literals into SQL
 * for evaluation against posts as part of a larger compiled CNF. This
 * compilation is specific to the derived classes, and hence presented as an
 * abstract method.
 */
abstract class Literal
{
    /**
     * @brief The $invert Boolean indicates whether the predicate is to be
     * inverted.
     *
     * If the $invert Boolean is set to FALSE, then the literal is to be used
     * as-is. If it is set to TRUE, however, then the literal is to be
     * inverted, i.e. have the NOT logical unary operator applied to it.
     */
    public bool $invert;

    protected function __construct(bool $invert)
    {
        $this->invert = $invert;
    }

    /**
     * @brief Create a CNF literal from the columns of a row from the
     * bubble_literals table.
     *
     * The names of the parameters correspond to the names of the database
     * columns that the arguments are supposed to originate from. The database
     * table for literals has some constraints on the values in these columns,
     * and this method assumes that those constraints hold. If they do not,
     * then an exception might be thrown, or a nonsensical result may be
     * returned.
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
     * @brief Compile the CNF literal into a SQL expression.
     *
     * The Cnf class has a method of the same name, and in fact the same
     * signature. Please check it out because it contains vital information on
     * how to use this method. Although be aware that that method calls this
     * one, so it is unlikely that you will ever have to.
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
     * @brief Compile the basic part of the CNF literal into an SQL expression.
     *
     * The “basic part”, in this case, refers to the properties of the CNF
     * literal <em>except</em> for the $invert property. The $invert property
     * is already taken care of by the to_sql_tokens() method, so this method
     * should always assume that the $invert property is set to FALSE.
     *
     * Apart from that, the usage of to_sql_tokens() applies.
     *
     * @param array<?string> $parameters
     * @return iterable<string>
     */
    protected abstract function to_sql_tokens_basic(string $posts, &$parameters);
}
