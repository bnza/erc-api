<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\Sample;
use App\Entity\Validator\UniqueSample;
use App\Repository\StratigraphicUnitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ValidatorUniqueSampleProvider implements ProviderInterface
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
         * @var $repo StratigraphicUnitRepository
         */
        $repo = $this->entityManager->getRepository(Sample::class);
        $unique = $repo->isUnique($criteria);

        return new UniqueSample($criteria, $unique);
    }
}
