<?php
declare(strict_types = 1);
namespace Bubble;

final class Main
{
    private function __construct()
    {
    }

    public static function main(): void
    {
        $state   = getcwd() . '/../../state';
        $pg_host = "$state/postgresql/sockets";
        pg_connect("
            host=$pg_host
            user=bubble_application
            password=bubble_application
            dbname=bubble
        ");
        echo 'Hello, world!';
    }
}
