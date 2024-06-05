<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\Site;
use App\Entity\Validator\Unique;
use App\Repository\CheckUniqueRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidatorUniqueProvider implements ProviderInterface
{
    private readonly array $resources;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->resources = [
            'site' => Site::class,
        ];
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!method_exists($operation, 'getDefaults')) {
            throw new \LogicException(sprintf('Operation %s should implement getDefaults()', $operation->getName()));
        }
        $defaults = $operation->getDefaults();
        if (
            !is_array($defaults)
            && !array_key_exists('resource', $defaults)
            || !array_key_exists('property', $defaults)
            || !array_key_exists($defaults['resource'], $this->resources)
        ) {
            throw new HttpException(404, 'Not found');
        }

        if (!array_key_exists('id', $uriVariables)) {
            throw new HttpException(400, 'Missing mandatory values');
        }

        $id = $uriVariables['id'];

        $className = $this->resources[$defaults['resource']];
        $property = $defaults['property'];

        $repo = $this->entityManager->getRepository($className);

        if (!$repo instanceof CheckUniqueRepositoryInterface)
        {
            throw new \LogicException(sprintf('Repository %s must implement %s', $className, CheckUniqueRepositoryInterface::class));
        }
        $unique = $repo->isUnique($property, $id);

        return new Unique($id, $unique);
    }
}
