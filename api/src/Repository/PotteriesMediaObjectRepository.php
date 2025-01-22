<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PotteriesMediaObjectRepository extends EntityRepository implements DuplicateMediaObjectEntityRepositoryInterface
{
    use DuplicateMediaObjectEntityRepositoryTrait;
}
