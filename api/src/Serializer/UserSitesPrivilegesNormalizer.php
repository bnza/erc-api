<?php

namespace App\Serializer;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function in_array;

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
                return $carry || in_array($group, ['read:session:User']);
            };

            return false;
            // return array_reduce($this->propertyAccessor->getValue($context, '[groups]'), $isSupportedContextGroup, false);
        };

        return $containsSupportedContextGroup($context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            // SitesUsers::class => false,
        ];
    }

    public function normalize(mixed $data, string $format = null, array $context = []): array
    {
        return [$data->site->getId() => $data->privilege];
    }
}
