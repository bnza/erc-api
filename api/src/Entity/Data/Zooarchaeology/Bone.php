<?php

namespace App\Entity\Data\Zooarchaeology;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Data\StratigraphicUnit;

#[ApiResource(
    shortName: 'Bone',
    operations: [
        new Get(),
        new GetCollection()
    ],
    routePrefix: 'zooarchaeology',
    normalizationContext: [
        'groups' => ['Zooarchaeology:Bone:acl:read']
    ],
    denormalizationContext: [
        'groups' => ['Zooarchaeology:Bone:item:write']
    ]
)]
class Bone
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    public string $taxonomy;

    public bool $unidentified = false;

    public ?string $notes;

    public function getId(): int
    {
        return $this->id;
    }
}
