<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractCheckUniqueRepository extends EntityRepository implements CheckUniqueRepositoryInterface
{
    use AbstractCheckUniqueRepositoryTrait;
}
