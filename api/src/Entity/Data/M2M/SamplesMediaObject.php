<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\Sample;
use App\Metadata\Attribute\MediaObjectJoinApiResource;
use Override;


#[MediaObjectJoinApiResource]
class SamplesMediaObject extends BaseMediaObjectJoin
{

    #[Override] function getItemClass(): string
    {
        return Sample::class;
    }
}
