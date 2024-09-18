<?php

namespace App\Repository;

use App\Entity\Data\MediaObject;

interface DuplicateMediaObjectEntityRepositoryInterface
{
    public function getDuplicate(string $sha256): ?MediaObject;
}
