<?php

namespace App\Entity\Geometry;

use App\Entity\Site as Site;

class SiteGeometry
{
    public Site $site;

    public string $geom;

    public function getId(): int
    {
        return $this->site->getId();
    }


}
