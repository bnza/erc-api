<?php

namespace App\Entity\Data;

use App\Entity\Data\View\M2M\VwStratigraphicUnitsSamples;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Sample
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    /**
     * @var Collection<int, VwStratigraphicUnitsSamples>
     */
    public Collection $stratigraphicUnits;

    public int $number;

    public ?string $collector;

    public ?DateTimeImmutable $takingDate;

    public ?string $description;

    public bool $public = false;

    public function __construct()
    {
        $this->stratigraphicUnits = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return sprintf(
            '%s/%u',
            $this->stratigraphicUnit->getCode(),
            $this->number
        );
    }
//    public function getSus()
//    {
//        $this->stratigraphicUnits->map(function (VwStratigraphicUnitsSamples $join) {
//            return ['id' => $join->stratigraphicUnit->getId(), 'code' => $join->stratigraphicUnit->getCode()];
//        });
//    }
}
