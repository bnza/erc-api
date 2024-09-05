<?php

namespace App\Serializer;

use App\Entity\Validator\Unique;
use App\Entity\Validator\UniqueInterface;
use App\Entity\Validator\UniqueSitesUsers;
use App\Entity\Validator\UniqueStratigraphicUnit;
use App\Entity\Validator\UniqueStratigraphicUnitsRelationship;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidatorUniqueNormalizer implements NormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof UniqueInterface;
    }

    public function getSupportedTypes(): array
    {
        return [
            Unique::class => true,
            UniqueStratigraphicUnit::class => true,
            UniqueStratigraphicUnitsRelationship::class => true,
            UniqueSitesUsers::class => true,
        ];
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): int
    {
        /*
         * @var $object UniqueInterface
         */
        return (int)$object->isUnique();
    }
}
