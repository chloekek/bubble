<?php
declare(strict_types = 1);
namespace Bubble\Support;

use Bubble\Support\Cnf\Literal;
use Bubble\Support\Postgresql;
use Traversable;

/**
 * The predicate of a bubble is encoded in conjunctive normal form [1]. This
 * class embodies that notion in AST form, and has methods to retrieve
 * predicates from the database and render them to SQL.
 *
 * Rendering a CNF AST to SQL allows it to be evaluated against posts, for
 * instance in the WHERE clause of an SQL query. It is expected that the query
 * planner in PostgreSQL will take care of optimizing the predicate using
 * indexes.
 *
 * [1]: https://en.wikipedia.org/wiki/Conjunctive_normal_form
 */
final class Cnf
{
    /**
     * Jagged multidimensional array of literals. The outer array is one of
     * disjunctions, the inner ones are of literals.
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
     * Fetch the CNF for a bubble. If the bubble does not exist, return the
     * empty CNF, which all posts match.
     *
     * The order of the disjunctions and literals is not guaranteed, and may be
     * different across calls.
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
                bubble.bubble_disjunctions AS bd
                INNER JOIN bubble.bubble_literals AS bl
                    ON bl.disjunction_id = bd.id

            WHERE
                bd.conjunction_id = $1
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
     * Compile the CNF AST into a SQL expression. The SQL expression has the
     * highest precedence, as it is wrapped in parentheses. Parameters will be
     * appended onto the given array, and the parameter indices (i.e. the
     * integers following the dollar signs) will be determined from the array’s
     * length.
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
     * Like to_sql, but return a generator of tokens instead. You should not
     * use $parameters until the generator is completely consumed.
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
