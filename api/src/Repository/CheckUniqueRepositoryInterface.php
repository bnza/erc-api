<?php

namespace App\Repository;

interface CheckUniqueRepositoryInterface
{
    public function isUnique(array $criteria): bool;
}
