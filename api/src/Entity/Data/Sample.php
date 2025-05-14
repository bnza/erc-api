<?php

namespace App\Entity\Data;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Data\View\M2M\VwStratigraphicUnitsSamples;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Sample implements MediaObjectsHolderInterface
{
    use MediaObjectsHolderTrait;

    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    /**
     * @var Collection<int, VwStratigraphicUnitsSamples>
     */
    public Collection $stratigraphicUnits;

    public int $number;

    public ?string $collector;

    public ?\DateTimeImmutable $takingDate;

    public ?string $description;

    #[ApiProperty(security: "is_granted('IS_AUTHENTICATED_FULLY')")]
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
}
