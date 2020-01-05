<?php
declare(strict_types = 1);
namespace Bubble;

use joshtronic\LoremIpsum;

final class Main
{
    private function __construct()
    {
    }

    public static function main(): void
    {
        $state   = getcwd() . '/../../state';
        $pg_host = "$state/postgresql/sockets";
        $db = new Support\Postgresql\Connection("
            host=$pg_host
            user=bubble_application
            password=bubble_application
            dbname=bubble
        ");

        $cnf = Support\Cnf::for_bubble($db, '8d96cc11-327e-48de-a7e9-af5654375a8d');
        \var_dump($cnf);

        $lipsum = new LoremIpsum;

        echo '<!DOCTYPE html><meta charset="utf-8"><title>Bubble</title>';
        echo '<link rel="stylesheet" href="/assets/index.css">';
        $view_timeline = new ViewTimeline\Response\Html(
            '/posts',
            new class implements ViewTimeline\Response\UrlProvider {
                public function all_url(): string
                {
                    return '/all';
                }

                public function bubble_url(string $id): string
                {
                    return '/all/' . Support\Web\Escape::u($id);
                }
            },
        );
        $view_timeline->render(
            [
                new ViewTimeline\Xact\Bubble('1', 'A'),
                new ViewTimeline\Xact\Bubble('2', 'B'),
            ],
            [
                new ViewTimeline\Xact\Post($lipsum->paragraph()),
                new ViewTimeline\Xact\Post($lipsum->paragraph()),
                new ViewTimeline\Xact\Post($lipsum->paragraph()),
                new ViewTimeline\Xact\Post($lipsum->paragraph()),
            ],
            '/x',
            '/y',
        );
    }
}
