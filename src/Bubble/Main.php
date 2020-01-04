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

        echo '<!DOCTYPE html><meta charset="utf-8"><title>Bubble</title>';
        echo '<link rel="stylesheet" href="/assets/index.css">';
        $view_timeline = new ViewTimeline\Html(
            '/posts',
            new class implements ViewTimeline\UrlProvider {
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
        $widgets = $view_timeline->render(
            [
                new ViewTimeline\Bubble('1', 'A'),
                new ViewTimeline\Bubble('2', 'B'),
            ],
            [
                new ViewTimeline\Post('α'),
                new ViewTimeline\Post('β'),
                new ViewTimeline\Post('γ'),
                new ViewTimeline\Post('δ'),
            ],
            '/x',
            '/y',
        );
        foreach ($widgets as $widget)
            $widget->toHtml();
    }
}
