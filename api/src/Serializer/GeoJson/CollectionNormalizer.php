<?php

namespace App\Serializer\GeoJson;

use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Serializer\AbstractCollectionNormalizer;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\PartialPaginatorInterface;

class CollectionNormalizer extends AbstractCollectionNormalizer
{
    public const FORMAT = 'geojson';

    private array $defaultContext = [];

    public function __construct(ResourceClassResolverInterface $resourceClassResolver, private ItemNormalizer $itemNormalizer, array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
        parent::__construct($resourceClassResolver, '');
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && parent::supportsNormalization($data, $format, $context);
    }

    protected function getPaginationData(iterable $object, array $context = []): array
    {
        $data['type'] = 'FeatureCollection';
        if ($object instanceof PaginatorInterface) {
            $data['totalItems'] = (int) $object->getTotalItems();
        }

        if (is_array($object) || ($object instanceof \Countable && !$object instanceof PartialPaginatorInterface)) {
            $data['totalItems'] = count($object);
        }

        return $data;
    }

    protected function getItemsData(iterable $object, ?string $format = null, array $context = []): array
    {
        $data = [];
        $data['features'] = [];
        foreach ($object as $obj) {
            $data['features'][] = $this->itemNormalizer->normalize($obj, $format, $context);
        }

        return $data;
    }
}
