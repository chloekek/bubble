<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Response;

use Bubble\Support\Web\Html as H;
use Bubble\ViewTimeline\Xact\Bubble;
use Bubble\ViewTimeline\Xact\Post;

/**
 * Render the HTML page for the “view timeline” use case.
 * See the Mockup.svg file for what this is supposed to resemble.
 * The file Html.scss houses the corresponding style sheet.
 */
final class Html
{
    private string $submit_url;
    private UrlProvider $url_provider;

    public function __construct(string $submit_url, UrlProvider $url_provider)
    {
        $this->submit_url = $submit_url;
        $this->url_provider = $url_provider;
    }

    /**
     * Render the entire page.
     *
     * @param Bubble[] $bubbles
     * @param iterable<Post> $posts
     */
    public function render($bubbles, $posts, ?string $previous_page_url,
                           ?string $next_page_url): void
    {
        $this->render_post_composer();
        $this->render_timeline_selector($bubbles);
        self::render_timeline($posts, $previous_page_url, $next_page_url);
    }

    /**
     * Render the form that allows the user to compose a new post
     * and publish it or save it as a draft.
     */
    public function render_post_composer(): void
    {
        H::open('form', [ 'class' => '--post-composer'
                        , 'method' => 'post'
                        , 'action' => $this->submit_url ]);

            // Body field.
            H::open('textarea', [ 'class' => '-body'
                                , 'name' => 'body' ]);
            H::close('textarea');

            // Publish button.
            H::open('button', [ 'class' => '-publish'
                              , 'name' => 'publish' ]);
                H::text('Publish');
            H::close('button');

            // Draft button.
            H::open('button', [ 'class' => '-draft'
                              , 'name' => 'draft' ]);
                H::text('Draft');
            H::close('button');

        H::close('form');
    }

    /**
     * Render the list of timelines that the user can navigate to.
     * This includes the special “all” timeline and the bubble timelines.
     *
     * @param Bubble[] $bubbles
     */
    public function render_timeline_selector($bubbles): void
    {
        H::open('nav', [ 'class' => '--timeline-selector' ]);
            $this->render_all_link();
            foreach ($bubbles as $bubble):
                $this->render_bubble_link($bubble);
            endforeach;
        H::close('nav');
    }

    private function render_all_link(): void
    {
        $url = $this->url_provider->all_url();
        H::open('a', [ 'class' => '-all'
                     , 'href' => $url ]);
            H::text('All');
        H::close('a');
    }

    private function render_bubble_link(Bubble $bubble): void
    {
        $url = $this->url_provider->bubble_url($bubble->id);
        H::open('a', [ 'class' => '-bubble'
                     , 'href' => $url ]);
            H::text($bubble->name);
        H::close('a');
    }

    /**
     * Render the list of posts in the timelime,
     * along with page navigation buttons if applicable.
     *
     * @param iterable<Post> $posts
     */
    public function render_timeline($posts, ?string $previous_page_url,
                                    ?string $next_page_url): void
    {
        H::open('section', [ 'class' => '--timeline' ]);
            foreach ($posts as $post):
                $this->render_post($post);
            endforeach;
            $this->render_page_selector($previous_page_url, $next_page_url);
        H::close('section');
    }

    private function render_post(Post $post): void
    {
        H::open('article', [ 'class' => '-post' ]);
            H::text($post->body);
        H::close('article');
    }

    private function render_page_selector(?string $previous_page_url,
                                          ?string $next_page_url): void
    {
        if ($previous_page_url === NULL &&
            $next_page_url === NULL)
            return;

        H::open('nav', [ 'class' => '-page-selector' ]);

            if ($previous_page_url !== NULL):
                H::open('a', [ 'class' => '-previous'
                             , 'href' => $previous_page_url ]);
                    H::text('Previous');
                H::close('a');
            endif;

            if ($next_page_url !== NULL):
                H::open('a', [ 'class' => '-next'
                             , 'href' => $next_page_url ]);
                    H::text('Next');
                H::close('a');
            endif;

        H::close('nav');
    }
}
