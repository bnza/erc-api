<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

abstract class AbstractCheckUniqueRepository extends EntityRepository implements CheckUniqueRepositoryInterface
{
    use AbstractCheckUniqueRepositoryTrait;

    public function exists(string|array $fields, mixed $values): bool
    {
        $qb = $this->createQueryBuilder('e');

        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (!is_string($field)) {
                    throw new InvalidArgumentException('Field name must be string or array');
                }
            }
            if (!is_array($values)) {
                throw new InvalidArgumentException('Value argument must be an array when field argument is an array');
            }
            if (count($values) !== count($fields)) {
                throw new InvalidArgumentException('Field argument and value argument count do not match');
            }
        } elseif (!is_string($fields)) {
            throw new InvalidArgumentException('Field argument must be a string when not an array');
        } else {
            $fields = [$fields];
            $values = [$values];
        }

//        $subQuery = $this->createQueryBuilder('sub')
//            ->select('1')
//            ->setMaxResults(1);
//
//        foreach ($fields as $i => $field) {
//            $param = "field_$i";
//            $subQuery->andWhere("sub.$field = :$param")
//                ->setParameter($param, $values[$i]);
//        }
//
//        $qb->select('1')
//            ->where($qb->expr()->exists($subQuery->getDQL()))
//            ->setMaxResults(1);

        $qb->select('1')
            ->setMaxResults(1);

        foreach ($fields as $i => $field) {
            $param = "field_$i";
            $qb->andWhere("e.$field = :$param")
                ->setParameter($param, $values[$i]);
        }

        return (bool)$qb->getQuery()->getOneOrNullResult();
    }
}
