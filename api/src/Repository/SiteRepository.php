<?php

namespace App\Repository;

class SiteRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return ['code', 'name'];
    }
}
