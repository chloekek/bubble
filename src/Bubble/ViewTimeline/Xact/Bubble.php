<?php
declare(strict_types = 1);
namespace Bubble\ViewTimeline\Xact;

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
