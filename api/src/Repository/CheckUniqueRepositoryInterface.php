<?php

namespace App\Repository;

interface CheckUniqueRepositoryInterface {
    public function isUnique(string $field, int|string $value): bool;
}
