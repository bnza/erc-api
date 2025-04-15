<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\MediaObject;
use App\Entity\Data\Sample;
use App\Metadata\Attribute\MediaObjectJoinApiResource;


#[MediaObjectJoinApiResource]
class SamplesMediaObject
{
    private $id;

    public Sample $item;

    public MediaObject $mediaObject;

    public ?string $description;

    public function getId(): int
    {
        return $this->id;
    }
}
