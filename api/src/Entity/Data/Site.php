<?php

namespace App\Entity\Data;

use ApiPlatform\Metadata\ApiProperty;

class Site
{
    private int $id;

    public string $code;

    public string $name;

    public ?string $description;

    #[ApiProperty(security: "is_granted('IS_AUTHENTICATED_FULLY')")]
    public bool $public = false;

    public iterable $users;

    public iterable $areas;

    public iterable $stratigraphicUnits;

    public function getId(): int
    {
        return $this->id;
    }
}
