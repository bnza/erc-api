<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\Pottery;
use App\Metadata\Attribute\MediaObjectJoinApiResource;
use Override;


#[MediaObjectJoinApiResource]
class PotteriesMediaObject extends BaseMediaObjectJoin
{

    #[Override] function getItemClass(): string
    {
        return Pottery::class;
    }
}
