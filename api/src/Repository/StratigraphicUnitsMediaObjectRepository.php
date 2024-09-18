<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class StratigraphicUnitsMediaObjectRepository extends EntityRepository implements DuplicateMediaObjectEntityRepositoryInterface
{
    use DuplicateMediaObjectEntityRepositoryTrait;
}
