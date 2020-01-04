<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline;

final class Post
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }
}
