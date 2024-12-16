<?php

namespace App\Entity\Data\View\M2M;

use App\Entity\Data\Sample;
use App\Entity\Data\StratigraphicUnit;

class VwStratigraphicUnitsSamples
{
    public string $id;

    public StratigraphicUnit $stratigraphicUnit;

    public Sample $sample;
}
