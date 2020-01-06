<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Xact;

use Bubble\Support\Cnf;
use Bubble\Support\Postgresql;

/**
 * @ingroup view_timeline
 */
final class QueryTimeline
{
    private Postgresql\Connection $db;

    public function __construct(Postgresql\Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Yield each post in a bubble. The posts are yold in reverse chronological
     * order; most recent post first.
     *
     * @return iterable<Post>
     */
    public function query_bubble_timeline(string $bubble_id)
    {
        $parameters = [];

        $cnf = Cnf::for_bubble($this->db, $bubble_id);
        $predicate = $cnf->to_sql('p', $parameters);

        $rows = $this->db->execute('
            SELECT
                p.body

            FROM
                bubble.posts AS p

            WHERE
                ' . $predicate . '
                AND p.published IS NOT NULL

            ORDER BY
                p.published DESC
        ', $parameters);

        foreach ($rows as list($body)) {
            assert($body !== NULL);
            yield new Post($body);
        }
    }
}
