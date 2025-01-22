<?php

namespace App\Repository;

use App\Entity\Data\MediaObject;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

trait DuplicateMediaObjectEntityRepositoryTrait
{
    public function getDuplicate(string $sha256): ?MediaObject
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->innerJoin(
                'App\Entity\Data\MediaObject',
                'm',
                Expr\Join::WITH,
                $qb->expr()->eq('u.mediaObject', 'm.id'),
            )
            ->where('m.sha256 = :sha256')
            ->setParameter('sha256', $sha256);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
