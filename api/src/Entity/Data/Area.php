<?php

namespace App\Entity\Data;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;

#[ApiResource(
    normalizationContext: ['groups' => ['Area:read']],
)]
class Area
{
    private int $id;

    public Site $site;

    public string $code;

    public ?string $description;

    public iterable $stratigraphicUnits;

    #[ApiProperty(security: 'is_granted("IS_AUTHENTICATED_FULLY")')]
    public bool $public = false;

    public function getId(): int
    {
        return $this->id;
    }
}
