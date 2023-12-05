<?php

namespace App\Entity\Data\View\Geometry;

use App\Entity\Data\Site;

class VwSiteGeometry
{
    public readonly int $id;

    public readonly Site $site;

    public readonly array $geom;
}
