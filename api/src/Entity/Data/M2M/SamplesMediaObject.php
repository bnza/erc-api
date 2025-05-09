<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\Sample;
use App\Metadata\Attribute\MediaObjectJoinApiResource;

#[MediaObjectJoinApiResource]
class SamplesMediaObject extends BaseMediaObjectJoin
{
    #[\Override]
    public function getItemClass(): string
    {
        return Sample::class;
    }
}
