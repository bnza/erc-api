<?php

namespace App\Repository;

class MicroStratigraphicUnitRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['sample', 'number']];
    }
}
