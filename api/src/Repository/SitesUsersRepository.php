<?php

namespace App\Repository;

class SitesUsersRepository extends AbstractCheckUniqueRepository
{
    protected function getUniqueFields(): array
    {
        return [['site', 'user']];
    }
}
