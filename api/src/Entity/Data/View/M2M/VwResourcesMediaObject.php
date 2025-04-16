<?php

namespace App\Entity\Data\View\M2M;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Data\MediaObject;

#[ApiResource(
    shortName: "resourceMediaObject",
    operations: [
        new Get(),
        new GetCollection(),
    ]
)]
class VwResourcesMediaObject
{
    private int $id;

    private int $item;

    private MediaObject $mediaObject;

    private string $resource;

    private ?string $description = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getItem(): int
    {
        return $this->item;
    }

    public function setItem(int $item): VwResourcesMediaObject
    {
        $this->item = $item;

        return $this;
    }

    public function getMediaObject(): MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(MediaObject $mediaObject): VwResourcesMediaObject
    {
        $this->mediaObject = $mediaObject;

        return $this;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function setResource(string $resource): VwResourcesMediaObject
    {
        $this->resource = $resource;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): VwResourcesMediaObject
    {
        $this->description = $description;

        return $this;
    }


}
