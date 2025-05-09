<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

trait AbstractCheckUniqueRepositoryTrait
{
    abstract protected function getUniqueFields(): array;

    protected function supportFields(array $fields): void
    {
        $isSupported = false;
        foreach ($this->getUniqueFields() as $uniqueFields) {
            if (is_string($uniqueFields)) {
                $uniqueFields = [$uniqueFields];
            }
            $isSupported = $fields === $uniqueFields;
            if ($isSupported) {
                break;
            }
        }
        if (!$isSupported) {
            throw new \InvalidArgumentException(sprintf("Unsupported unique fields '%s'", implode(', ', $fields)));
        }
    }

    public function isUnique(array $criteria): bool
    {
        $this->supportFields(array_keys($criteria));

        $qb = $this->createQueryBuilder('u');
        $sub = $this->createQueryBuilder('s');

        $sub->select('1');
        $params = new ArrayCollection();

        foreach ($criteria as $field => $value) {
            $paramKey = ':'.$field;
            $params->add(new Parameter($paramKey, $value));
            $sub->andWhere($sub->expr()->eq($sub->getRootAliases()[0].'.'.$field, $paramKey));
        }

        $query = $qb->select('1')
            ->where($qb->expr()->exists($sub->getDQL()))
            ->setParameters($params)
            ->setMaxResults(1)
            ->getQuery();

        return !(bool) count($query->getResult());
    }
}
