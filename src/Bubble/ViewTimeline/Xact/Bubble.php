<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Xact;

/**
 * @ingroup view_timeline
 *
 * @brief The Bubble class implements a POD type for bubbles <em>for use in the
 * “view timeline” use case</em>.
 *
 * @snippet{doc} Bubble/README.php pod
 */
final class Bubble
{
    public string $id;
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
