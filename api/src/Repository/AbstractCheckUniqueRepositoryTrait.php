<?php

namespace App\Repository;

trait AbstractCheckUniqueRepositoryTrait
{
    abstract protected function getUniqueFields(): array;

    protected function supportField(string $field): void
    {
        if (!in_array($field, $this->getUniqueFields())) {
            throw new \InvalidArgumentException("Unsupported unique field '$field'");
        }
    }

    public function isUnique(string $field, int|string $value): bool
    {
        $this->supportField($field);

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
