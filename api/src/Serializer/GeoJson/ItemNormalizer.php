<?php

namespace App\Serializer\GeoJson;

use App\Entity\Data\View\Geometry\VwSiteGeometry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ItemNormalizer implements NormalizerInterface
{
    public const FORMAT = 'geojson';

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $decorated)
    {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
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

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return self::FORMAT === $format
            && is_object($data)
            && property_exists($data, 'geom');
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            VwSiteGeometry::class => true,
        ];
    }
}
