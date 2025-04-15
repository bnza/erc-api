<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SamplesMediaObjectRepository extends EntityRepository implements DuplicateMediaObjectEntityRepositoryInterface
{
    use DuplicateMediaObjectEntityRepositoryTrait;
}
