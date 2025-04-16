<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\StratigraphicUnit;
use App\Metadata\Attribute\MediaObjectJoinApiResource;
use Override;


#[MediaObjectJoinApiResource]
class StratigraphicUnitsMediaObject extends BaseMediaObjectJoin
{

    #[Override] function getItemClass(): string
    {
        return StratigraphicUnit::class;
    }
}
