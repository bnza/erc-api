<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Data\Site;
use App\Entity\Validator\Unique;
use Doctrine\ORM\EntityManagerInterface;
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
        $defaults = $operation->getDefaults();
        if (
            !is_array($defaults)
            && !array_key_exists('resource', $defaults)
            || !array_key_exists('property', $defaults)
            || !array_key_exists($defaults['resource'], $this->resources)
        ) {
            throw new HttpException(404, 'Not found');
        }

        if (!array_key_exists('value', $uriVariables)) {
            throw new HttpException(400, 'Missing mandatory values');
        }

        $value = $uriVariables['value'];

        $className = $this->resources[$defaults['resource']];
        $property = $defaults['property'];

        $repo = $this->entityManager->getRepository($className);

        $unique = $repo->isUnique($property, $value);

        return new Unique($unique);
    }
}
