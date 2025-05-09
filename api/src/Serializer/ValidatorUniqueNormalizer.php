<?php

namespace App\Serializer;

use App\Entity\Validator\Unique;
use App\Entity\Validator\UniqueInterface;
use App\Entity\Validator\UniqueMicroStratigraphicUnit;
use App\Entity\Validator\UniquePottery;
use App\Entity\Validator\UniqueSample;
use App\Entity\Validator\UniqueSitesUsers;
use App\Entity\Validator\UniqueStratigraphicUnit;
use App\Entity\Validator\UniqueStratigraphicUnitsRelationship;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidatorUniqueNormalizer implements NormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof UniqueInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Unique::class => true,
            UniqueSample::class => true,
            UniquePottery::class => true,
            UniqueStratigraphicUnit::class => true,
            UniqueMicroStratigraphicUnit::class => true,
            UniqueStratigraphicUnitsRelationship::class => true,
            UniqueSitesUsers::class => true,
        ];
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): int
    {
        /*
         * @var $object UniqueInterface
         */
        return (int) $data->isUnique();
    }
}
