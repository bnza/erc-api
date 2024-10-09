<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\Pottery;
use App\Entity\Validator\UniquePottery;
use App\Repository\PotteryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ValidatorUniquePotteryProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $criteria = [];
        foreach (['stratigraphicUnit', 'number'] as $prop) {
            if (!isset($uriVariables[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' parameter");
            }
            $criteria[$prop] = $uriVariables[$prop];
        }

        /**
         * @var $repo PotteryRepository
         */
        $repo = $this->entityManager->getRepository(Pottery::class);
        $unique = $repo->isUnique($criteria);

        return new UniquePottery($criteria, $unique);
    }
}
