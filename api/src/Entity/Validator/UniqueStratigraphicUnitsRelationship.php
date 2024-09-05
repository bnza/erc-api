<?php

namespace App\Entity\Validator;

readonly class UniqueStratigraphicUnitsRelationship extends AbstractUnique
{
    public int $sxSU;
    public int $dxSU;

    public function __construct(array $criteria, public bool $unique)
    {
        foreach (['sxSU', 'dxSU'] as $prop) {
            if (!isset($criteria[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' criteria");
            }
            $this->$prop = $criteria[$prop];
        }
    }

    public function getId(): string|int
    {
        return sprintf('%s.%s', $this->sxSU, $this->dxSU);
    }
}
