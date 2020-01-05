<?php
declare(strict_types = 1);
namespace Bubble\Support\Postgresql;

/**
 * Type-safe wrapper around pgsql connection resources.
 */
final class Connection
{
    /**
     * @var resource
     */
    private $raw;

    /**
     * Connect to a PostgreSQL database and return the connection handle. For
     * more information, see \pg_connect and the libpq manual.
     */
    public function __construct(string $dsn)
    {
        $this->raw = \pg_connect($dsn);
    }

    /**
     * Execute a query and lazily yield the rows. The query is always executed,
     * even if you do not consume the returned iterator.
     *
     * @param array<?string> $parameters
     * @return iterable<array<?string>>
     */
    public function execute(string $sql, $parameters)
    {
        $result = \pg_query_params($this->raw, $sql, $parameters);
        return self::yield_rows($result);
    }

    /**
     * Yield the rows in a result. While trivial, this is a separate method so
     * that \pg_query_params is invoked even if the generator is not consumed.
     *
     * @param resource $result
     * @return iterable<array<?string>>
     */
    private static function yield_rows($result)
    {
        for (;;) {
            /** @var false|array<?string> */
            $row = \pg_fetch_row($result);
            if ($row === FALSE)
                break;
            yield $row;
        }
    }
}
