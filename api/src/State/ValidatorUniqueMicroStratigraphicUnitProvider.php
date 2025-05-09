<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\MicroStratigraphicUnit;
use App\Entity\Validator\UniqueMicroStratigraphicUnit;
use App\Repository\StratigraphicUnitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ValidatorUniqueMicroStratigraphicUnitProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $criteria = [];
        foreach (['sample', 'number'] as $prop) {
            if (!isset($uriVariables[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' parameter");
            }
            $criteria[$prop] = $uriVariables[$prop];
        }

        /**
         * @var $repo StratigraphicUnitRepository
         */
        $repo = $this->entityManager->getRepository(MicroStratigraphicUnit::class);
        $unique = $repo->isUnique($criteria);

        return new UniqueMicroStratigraphicUnit($criteria, $unique);
    }
}
