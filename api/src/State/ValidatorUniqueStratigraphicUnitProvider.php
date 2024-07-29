<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\StratigraphicUnit;
use App\Entity\Validator\UniqueStratigraphicUnit;
use App\Repository\StratigraphicUnitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ValidatorUniqueStratigraphicUnitProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $criteria = [];
        foreach (['site', 'year', 'number'] as $prop) {
            if (!isset($uriVariables[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' parameter");
            }
            $criteria[$prop] = $uriVariables[$prop];
        }

        /**
         * @var $repo StratigraphicUnitRepository
         */
        $repo = $this->entityManager->getRepository(StratigraphicUnit::class);
        $unique = $repo->isUnique($criteria);

        return new UniqueStratigraphicUnit($criteria, $unique);
    }
}
