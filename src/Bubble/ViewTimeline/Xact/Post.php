<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Xact;

/**
 * @ingroup view_timeline
 *
 * @brief The Post class implements a POD type for posts <em>for use in the
 * “view timeline” use case</em>.
 *
 * @snippet{doc} Bubble/README.php pod
 */
final class Post
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }
}
