<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline;

use Bubble\Support\Web\Widget;
use Bubble\Support\Web\Widget\Html as H;

/**
 * Render the HTML page for the “view timeline” use case.
 * See the mockup for what this is supposed to resemble.
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
     * @param Post[] $posts
     * @return Widget[]
     */
    public function render($bubbles, $posts, ?string $previous_page_url,
                           ?string $next_page_url)
    {
        return [
            $this->render_post_composer(),
            $this->render_timeline_selector($bubbles),
            self::render_timeline($posts, $previous_page_url, $next_page_url),
        ];
    }

    /**
     * Render the form that allows the user to compose a new post
     * and publish it or save it as a draft.
     */
    public function render_post_composer(): Widget
    {
        return H::form(
            [ 'class' => '--post-composer'
            , 'method' => 'post'
            , 'action' => $this->submit_url ],
            H::textarea(
                [ 'class' => '-body'
                , 'name' => 'body' ],
            ),
            H::button(
                [ 'class' => '-publish'
                , 'name' => 'publish' ],
                H::text('Publish'),
            ),
            H::button(
                [ 'class' => '-draft'
                , 'name' => 'draft' ],
                H::text('Draft'),
            ),
        );
    }

    /**
     * Render the list of timelines that the user can navigate to.
     * This includes the special “all” timeline and the bubble timelines.
     *
     * @param Bubble[] $bubbles
     */
    public function render_timeline_selector($bubbles): Widget
    {
        return H::nav(
            [ 'class' => '--timeline-selector' ],
            $this->render_all_link(),
            ...\array_map([$this, 'render_bubble_link'], $bubbles),
        );
    }

    private function render_all_link(): Widget
    {
        $url = $this->url_provider->all_url();
        return H::a(
            [ 'class' => '-all'
            , 'href' => $url ],
            H::text('All'),
        );
    }

    private function render_bubble_link(Bubble $bubble): Widget
    {
        $url = $this->url_provider->bubble_url($bubble->id);
        return H::a(
            [ 'class' => '-bubble'
            , 'href' => $url ],
            H::text($bubble->name),
        );
    }

    /**
     * Render the list of posts in the timelime,
     * along with page navigation buttons if applicable.
     *
     * @param Post[] $posts
     */
    public function render_timeline($posts, ?string $previous_page_url,
                                    ?string $next_page_url): Widget
    {
        return H::section(
            [ 'class' => '--timeline' ],
            ...\array_map([$this, 'render_post'], $posts),
            ...$this->render_page_selector($previous_page_url, $next_page_url),
        );
    }

    private function render_post(Post $post): Widget
    {
        return H::article(
            [ 'class' => '-post' ],
            H::text($post->body),
        );
    }

    /**
     * @return Widget[]
     */
    private function render_page_selector(?string $previous_page_url,
                                          ?string $next_page_url)
    {
        $page_links = [];

        if ($previous_page_url !== NULL)
            $page_links[] = H::a(
                [ 'class' => '-previous'
                , 'href' => $previous_page_url ],
                H::text('Previous'),
            );

        if ($next_page_url !== NULL)
            $page_links[] = H::a(
                [ 'class' => '-next'
                , 'href' => $next_page_url ],
                H::text('Next'),
            );

        if (\count($page_links) === 0) {
            return [];
        } else {
            $nav = H::nav(
                [ 'class' => '-page-selector' ],
                ...$page_links,
            );
            return [$nav];
        }
    }
}
