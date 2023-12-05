<?php

namespace App\Entity\Data\Geometry;

use App\Entity\Data\Site;

class SiteGeometry
{
    public Site $site;

    public string $geom;

    public function getId(): int
    {
        return $this->site->getId();
    }
}
