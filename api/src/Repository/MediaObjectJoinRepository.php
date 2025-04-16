<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MediaObjectJoinRepository extends EntityRepository implements DuplicateMediaObjectEntityRepositoryInterface
{
    use DuplicateMediaObjectEntityRepositoryTrait;
}
