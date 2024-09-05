<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\View\M2M\VwStratigraphicUnitsRelationship;
use App\Entity\Validator\UniqueStratigraphicUnitsRelationship;
use App\Repository\StratigraphicUnitsRelationshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class ValidatorUniqueStratigraphicUnitsRelationshipProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $criteria = [];
        foreach (['sxSU', 'dxSU'] as $prop) {
            if (!isset($uriVariables[$prop])) {
                throw new InvalidArgumentException("Missing '$prop' parameter");
            }
            $criteria[$prop] = $uriVariables[$prop];
        }

        /**
         * @var $repo StratigraphicUnitsRelationshipRepository
         */
        $repo = $this->entityManager->getRepository(VwStratigraphicUnitsRelationship::class);
        $unique = $repo->isUnique($criteria);

        return new UniqueStratigraphicUnitsRelationship($criteria, $unique);
    }
}
