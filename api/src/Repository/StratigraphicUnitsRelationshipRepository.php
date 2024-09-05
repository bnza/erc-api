<?php

namespace App\Repository;

class StratigraphicUnitsRelationshipRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['sxSU', 'dxSU']];
    }
}
