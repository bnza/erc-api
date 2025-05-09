<?php

namespace App\Entity\Data\View\Geometry;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Filter\SiteGeometryFilter;

#[ApiResource(
    uriTemplate: '/geometry/sites.{_format}',
    shortName: 'SiteGeometry',
    operations: [
        new GetCollection(),
    ],
    formats: ['geojson' => ['application/geo+json'], 'json' => ['application/json']],
    defaults: ['_format' => 'json'],
    normalizationContext: ['groups' => ['SiteGeometry:read']],
)]
#[ApiFilter(SiteGeometryFilter::class)]
readonly class VwSiteGeometry
{
    public int $id;

    public string $code;

    public string $name;

    public ?string $description;

    public bool $public;

    public array $geom;
}
