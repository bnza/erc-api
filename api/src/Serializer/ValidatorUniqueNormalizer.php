<?php

namespace App\Serializer;

use App\Entity\Validator\Unique;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidatorUniqueNormalizer implements NormalizerInterface
{
    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Unique;
    }

    public function getSupportedTypes(): array
    {
        return [
           Unique::class => true,
        ];
    }

    public function normalize(mixed $object, string $format = null, array $context = []): int
    {
        return (int) $object->unique;
    }
}
