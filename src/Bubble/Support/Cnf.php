<?php
declare(strict_types = 1);
namespace Bubble\Support;

use Bubble\Support\Cnf\Literal;
use Bubble\Support\Postgresql;

/**
 * Conjunctive normal form representation of a Bubble predicate. A CNF AST can
 * be evaluated against a post, resulting in a Boolean.
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
}
