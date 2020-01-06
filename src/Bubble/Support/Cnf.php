<?php
declare(strict_types = 1);
namespace Bubble\Support;

use Bubble\Support\Cnf\Literal;
use Bubble\Support\Postgresql;
use Traversable;

/**
 * @defgroup cnf CNF
 *
 * @brief Each bubble has a predicate that decides which posts are a member of the
 * bubble and which are not. Such a predicate is also known as “a CNF”, due to
 * its representation, <em>conjunctive normal form</em>.
 *
 * In short, CNF has the syntax listed in figure 1. This syntax is embodied by
 * the classes in this module. These classes also have useful methods for
 * working with CNFs, so please familiarize yourself with them.
 *
 * Figure 1:
 *
 * @code
 * <cnf>         ::= <conjunction>
 * <conjunction> ::= <disjunction>*
 * <disjunction> ::= <literal>+
 * <literal>     ::= ‘not’? (<author> | <tag>)
 * @endcode
 *
 * Evaluating a CNF against a post proceeds as follows:
 *
 * - A conjunction evaluates to true iff all of the disjunctions inside it
 *   evaluate to true.
 * - A disjunction evaluates to true iff any of the literals inside it evaluate
 *   to true.
 * - Whether a literal evaluates to true depends on the literal. See the
 *   derived classes of the Literal class for more information.
 *
 * You can learn more about conjunctive normal form in [an encyclopedia article
 * about it][cnf].
 *
 * [cnf]: https://en.wikipedia.org/wiki/Conjunctive_normal_form
 */

/**
 * @ingroup cnf
 *
 * @brief The Cnf class implements a data structure for CNFs.
 *
 * The Cnf class has methods to retrieve CNFs from the database, as well as
 * methods to compile CNFs into SQL for evaluation against posts.
 */
final class Cnf
{
    /**
     * @brief Jagged multidimensional array of literals. The outer array is one
     * of disjunctions, the inner ones are of literals.
     *
     * @var Literal[][]
     */
    public $cnf;

    /**
     * @param Literal[][] $cnf
     */
    public function __construct($cnf)
    {
        $this->cnf = $cnf;
    }

    /**
     * @brief Fetch the CNF that belongs to a certain bubble from the database
     * using the given database connection.
     *
     * The order of the disjunctions and literals is not guaranteed, and may be
     * different across calls. Luckily, the order is not important for the
     * meaning of the CNF: conjunctions and disjunctions are fundamentally
     * devoid of ordering.
     */
    public static function for_bubble(Postgresql\Connection $db,
                                      string $bubble_id): Cnf
    {
        $result = $db->execute('
            SELECT
                bl.disjunction_id,
                bl.invert,
                bl.assert_author_id,
                bl.assert_tag

            FROM
                bubble.bubble_literals AS bl

            WHERE
                bl.conjunction_id = $1
        ', [$bubble_id]);

        $cnf = [];

        foreach ($result as list($disjunction_id, $invert,
                                 $assert_author_id, $assert_tag)) {
            assert($disjunction_id !== NULL);
            assert($invert !== NULL);
            $cnf[$disjunction_id][] =
                Literal::from_row($invert === 't',
                                  $assert_author_id,
                                  $assert_tag);
        }

        return new Cnf(\array_values($cnf));
    }

    /**
     * @brief Compile the CNF AST into a SQL expression so that it can be
     * evaluated against posts.
     *
     * The SQL expression is returned as a string. The expression will refer to
     * parameters using the usual PostgreSQL dollar sign notation. The values
     * of these parameters will be appended onto the given array, and the
     * parameter indices will be determined from the array’s length.
     *
     * The SQL expression has the highest precedence. Therefore, the caller
     * does not need to wrap it in parentheses to avoid conflicting fixities
     * and precedences when embedding the expression into a larger expression.
     *
     * @param string $posts The SQL identifier that names the posts table.
     * @param array<?string> $parameters
     */
    public function to_sql(string $posts, &$parameters): string
    {
        $tokens = $this->to_sql_tokens($posts, $parameters);
        return \implode(' ', \iterator_to_array($tokens, FALSE));
    }

    /**
     * @brief Like to_sql, but return a generator of tokens instead.
     *
     * You should not use $parameters until the generator is completely
     * consumed.
     *
     * @param array<?string> $parameters
     * @return Traversable<string>
     */
    public function to_sql_tokens(string $posts, &$parameters)
    {
        yield '(';
        yield 'TRUE';
            foreach ($this->cnf as $disjunction):
                yield 'AND';
                yield '(';
                yield 'FALSE';
                    foreach ($disjunction as $literal):
                        yield 'OR';
                        yield from $literal->to_sql_tokens($posts, $parameters);
                    endforeach;
                yield ')';
            endforeach;
        yield ')';
    }
}
