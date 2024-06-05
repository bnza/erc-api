<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractCheckUniqueRepository extends EntityRepository implements CheckUniqueRepositoryInterface
{
    abstract protected function getUniqueFields(): array;

    protected function supportField(string $field): void
    {
        if (!in_array($field, $this->getUniqueFields())) {
            throw new \InvalidArgumentException("Unsupported unique field '$field'");
        }
    }
}
