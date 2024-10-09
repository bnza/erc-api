<?php

namespace App\Repository;

class PotteryRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['stratigraphicUnit', 'number']];
    }
}
