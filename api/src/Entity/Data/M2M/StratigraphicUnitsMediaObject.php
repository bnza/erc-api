<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\StratigraphicUnit;
use App\Metadata\Attribute\MediaObjectJoinApiResource;

#[MediaObjectJoinApiResource]
class StratigraphicUnitsMediaObject extends BaseMediaObjectJoin
{
    #[\Override]
    public function getItemClass(): string
    {
        return StratigraphicUnit::class;
    }
}
