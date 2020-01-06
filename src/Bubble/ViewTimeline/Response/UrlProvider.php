<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Response;

/**
 * @ingroup view_timeline
 *
 * Provide URLs to endpoints for the “view timeline” use case.
 */
interface UrlProvider
{
    /**
     * Provide the URL to the special “all” timeline.
     */
    function all_url(): string;

    /**
     * Provide the URL to the timeline for a particular bubble.
     */
    function bubble_url(string $id): string;
}
