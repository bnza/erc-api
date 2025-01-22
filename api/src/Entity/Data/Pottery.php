<?php

namespace App\Entity\Data;

use App\Entity\Data\Vocabulary\Pottery\FunctionalGroup;
use App\Entity\Data\Vocabulary\Pottery\Part;
use App\Entity\Data\Vocabulary\Pottery\Typology;

class Pottery
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    public int $number;

    public ?string $projectIdentifier;

    public ?int $chronologyLower;

    public ?int $chronologyUpper;

    public int $fragmentsNumber = 1;

    public ?Part $part;

    public FunctionalGroup $functionalGroup;

    public Typology $typology;

    public ?string $description;

    public bool $public = true;

    public iterable $mediaObjects;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return sprintf('%s/%u', $this->stratigraphicUnit->getCode(), $this->number);
    }
}
