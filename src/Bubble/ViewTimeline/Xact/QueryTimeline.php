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
        // Step 2: Turn CNF AST into SQL expression and parameter list.
        // Step 3: Execute query.
        // Step 4: Return posts.
        return [];
    }
}
