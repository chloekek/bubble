<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Xact;

use Bubble\Support\Cnf;
use Bubble\Support\Postgresql;

final
class QueryTimeline
{
    private Postgresql\Connection $db;

    public function __construct(Postgresql\Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return Post[]
     */
    public function query_bubble_timeline(string $bubble_id)
    {
        $cnf = Cnf::for_bubble($this->db, $bubble_id);
        \var_dump($cnf);

        $parameters = [];
        $sql = $cnf->to_sql('p', $parameters);
        \var_dump($sql);
        \var_dump($parameters);

        // TODO: Create query.
        // TODO: Execute query.
        // TODO: Return posts.

        return [];
    }
}
