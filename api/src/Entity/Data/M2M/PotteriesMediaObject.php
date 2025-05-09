<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\Pottery;
use App\Metadata\Attribute\MediaObjectJoinApiResource;

#[MediaObjectJoinApiResource]
class PotteriesMediaObject extends BaseMediaObjectJoin
{
    #[\Override]
    public function getItemClass(): string
    {
        return Pottery::class;
    }
}
