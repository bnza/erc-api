<?php

namespace App\Repository;

class SampleRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['stratigraphicUnit', 'number']];
    }
}
