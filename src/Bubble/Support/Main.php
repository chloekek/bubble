<?php
declare(strict_types = 1);
namespace Bubble\Support;

use Bubble\ViewTimeline;
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
        $db = new Postgresql\Connection("
            host=$pg_host
            user=bubble_application
            password=bubble_application
            dbname=bubble
        ");

        $queryTimeline = new ViewTimeline\Xact\QueryTimeline($db);
        $posts = $queryTimeline->query_bubble_timeline('8d96cc11-327e-48de-a7e9-af5654375a8d');

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
                    return '/all/' . Web\Escape::u($id);
                }
            },
        );
        $view_timeline->render(
            [
                new ViewTimeline\Xact\Bubble('1', 'A'),
                new ViewTimeline\Xact\Bubble('2', 'B'),
            ],
            $posts,
            '/x',
            '/y',
        );
    }
}
