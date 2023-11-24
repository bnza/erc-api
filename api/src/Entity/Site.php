<?php

namespace App\Entity;
use App\Entity\Geometry\SiteGeometry;

class Site
{
    private int $id;

    public string $code;

    public string $name;

    public string $description;

    public bool $public = false;

    public iterable $users;

    public function getId(): int
    {
        return $this->id;
    }
}
