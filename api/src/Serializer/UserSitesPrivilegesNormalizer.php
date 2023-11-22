<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use App\Entity\M2M\SitesUsers;
class UserSitesPrivilegesNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        $containsSupportedContextGroup = function (array $context = []) {
            $isSupportedContextGroup = function (bool $carry, string $group): bool {
                return $carry || \in_array($group, ['read:session:User']);
            };
            return false;
           // return array_reduce($this->propertyAccessor->getValue($context, '[groups]'), $isSupportedContextGroup, false);
        };
        return $containsSupportedContextGroup($context);
    }

    public function getSupportedTypes(): array {
        return [
           // SitesUsers::class => false,
        ];
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [$object->site->getId() => $object->privilege];
    }
}
