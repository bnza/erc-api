<?php

namespace App\Repository;

class StratigraphicUnitRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['site', 'year', 'number']];
    }
}
