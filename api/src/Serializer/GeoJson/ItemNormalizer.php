<?php

namespace App\Serializer\GeoJson;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @method array getSupportedTypes(?string $format)
 */
class ItemNormalizer implements NormalizerInterface
{
    public const FORMAT = 'geojson';

    public function __construct(private readonly ObjectNormalizer $decorated)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $data = $this->decorated->normalize($object, $format, $context);
        $geom = $data['geom'];
        unset($data['geom']);

        return [
            'type' => 'Feature',
            'id' => $data['id'],
            'geometry' => $geom,
            'properties' => $data,
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return self::FORMAT === $format
            && is_object($data)
            && property_exists($data, 'geom');
    }
}
