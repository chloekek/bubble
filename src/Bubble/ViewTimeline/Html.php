<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline;

use Bubble\Support\Web\Escape as E;

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
        ?>
            <form class="--post-composer" method="post"
                  action="<?= E::h($this->submit_url) ?>">
                <textarea class="-body" name="body"></textarea>
                <button class="-publish" name="publish">Publish</button>
                <button class="-draft" name="draft">Draft</button>
            </form>
        <?php
    }

    /**
     * Render the list of timelines that the user can navigate to.
     * This includes the special “all” timeline and the bubble timelines.
     *
     * @param Bubble[] $bubbles
     */
    public function render_timeline_selector($bubbles): void
    {
        ?>
            <nav class="--timeline-selector">
                <?php $this->render_all_link(); ?>
                <?php foreach ($bubbles as $bubble): ?>
                    <?php $this->render_bubble_link($bubble); ?>
                <?php endforeach; ?>
            </nav>
        <?php
    }

    private function render_all_link(): void
    {
        $url = $this->url_provider->all_url();
        ?>
            <a class="-all" href="<?= E::h($url) ?>">
                All</a>
        <?php
    }

    private function render_bubble_link(Bubble $bubble): void
    {
        $url = $this->url_provider->bubble_url($bubble->id);
        ?>
            <a class="-bubble" href="<?= E::h($url) ?>">
                <?= E::h($bubble->name) ?></a>
        <?php
    }

    /**
     * Render the list of posts in the timelime,
     * along with page navigation buttons if applicable.
     *
     * @param Post[] $posts
     */
    public function render_timeline($posts, ?string $previous_page_url,
                                    ?string $next_page_url): void
    {
        ?>
            <section class="--timeline">
                <?php foreach ($posts as $post): ?>
                    <article class="-post">
                        <?= E::h($post->body) ?>
                    </article>
                <?php endforeach; ?>

                <?php if ($previous_page_url !== NULL ||
                          $next_page_url !== NULL): ?>
                    <nav class="-page-selector">
                        <?php if ($previous_page_url !== NULL): ?>
                            <a class="-previous"
                               href="<?= E::h($previous_page_url) ?>">
                                Previous</a>
                        <?php endif; ?>
                        <?php if ($next_page_url !== NULL): ?>
                            <a class="-next"
                               href="<?= E::h($next_page_url) ?>">
                                Next</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </section>
        <?php
    }
}
