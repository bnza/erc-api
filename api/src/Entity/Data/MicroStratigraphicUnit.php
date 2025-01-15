<?php

namespace App\Entity\Data;

class MicroStratigraphicUnit
{
    private int $id;
    public Sample $sample;
    public StratigraphicUnit $stratigraphicUnit;
    public ?int $number;
    public string $depositType;

    public ?string $keyAttributes;

    public int $inclusionsGeology = 0;
    public int $inclusionsBuildingMaterials = 0;
    public int $inclusionsDomesticRefuse = 0;
    public int $inclusionsOrganicRefuse = 0;

    public string $colourPpl;
    public string $colourXpl;
    public string $colourOil;

    public bool $lenticularPlateyPeds = false;
    public bool $crumbsOrGranules = false;
    public bool $saBlockyPeds = false;
    public bool $cracks = false;
    public bool $infillings = false;

    public int $mesofaunaRootBioturbation = 0;
    public int $earthwormInternalChamber = 0;
    public int $organicOrganoMineral = 0;
    public int $earthwormGranule = 0;

    public ?string $interpretation;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return isset($this->number) ? sprintf(
            '%s/%u',
            $this->sample->getCode(),
            $this->number
        ) : $this->sample->getCode();
    }
}
