<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\M2M\SitesUsers;
use App\Entity\Validator\UniqueSitesUsers;
use App\Repository\StratigraphicUnitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ValidatorUniqueSitesUsersProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $criteria = [];
        foreach (['site', 'user'] as $prop) {
            if (!isset($uriVariables[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' parameter");
            }
            $criteria[$prop] = $uriVariables[$prop];
        }

        /**
         * @var $repo StratigraphicUnitRepository
         */
        $repo = $this->entityManager->getRepository(SitesUsers::class);
        $unique = $repo->isUnique($criteria);

        return new UniqueSitesUsers($criteria, $unique);
    }
}
