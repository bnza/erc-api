<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SiteRepository extends EntityRepository
{
    public function isUnique(string $field, int|string $value): bool
    {
        if (!in_array($field, ['code', 'name'])) {
            throw new \InvalidArgumentException("Unsupported unique field '$field'");
        }

        $qb = $this->createQueryBuilder('u');
        $sub = $this->createQueryBuilder('s');

        $sub->select('1')
            ->where($sub->expr()->eq($sub->getRootAliases()[0].'.'.$field, ':value'));
        $query = $qb->select('1')
            ->where($qb->expr()->exists($sub->getDQL()))
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->getQuery();

        return !(bool) count($query->getResult());
    }
}
