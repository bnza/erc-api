<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Data\View\Geometry\VwSiteGeometry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyInfo\Type;

final class BboxGeometryFilter extends AbstractFilter
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
    ) {
        parent::__construct($managerRegistry, $logger, $properties);
    }

    protected function filterProperty(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (VwSiteGeometry::class !== $resourceClass || 'bbox' !== $property) {
            return;
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $bboxParts = explode(',', $value);

        if (count($bboxParts) < 4) {
            return;
        }

        // Extract and validate the bbox values
        $minx = filter_var($bboxParts[0], FILTER_VALIDATE_FLOAT);
        $miny = filter_var($bboxParts[1], FILTER_VALIDATE_FLOAT);
        $maxx = filter_var($bboxParts[2], FILTER_VALIDATE_FLOAT);
        $maxy = filter_var($bboxParts[3], FILTER_VALIDATE_FLOAT);

        $srid = isset($bboxParts[4]) ? filter_var($bboxParts[4], FILTER_VALIDATE_INT) : 4326;

        if (false === $minx || false === $miny || false === $maxx || false === $maxy || false === $srid) {
            return; // Invalid values
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf(
            'ST_Intersects(ST_GeomFromGeoJSON(%s.geom), ST_MakeEnvelope(:minx, :miny, :maxx, :maxy, :srid)) = true',
            $rootAlias
        ))
            ->setParameter('minx', $minx)
            ->setParameter('miny', $miny)
            ->setParameter('maxx', $maxx)
            ->setParameter('maxy', $maxy)
            ->setParameter('srid', $srid);
    }

    public function getDescription(string $resourceClass): array
    {
        if (VwSiteGeometry::class !== $resourceClass) {
            return [];
        }

        return [
            'bbox' => [
                'property' => 'bbox',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Filter by bounding box (format: minx,miny,maxx,maxy[,srid]). SRID is optional and defaults to 4326.',
                'openapi' => [
                    'example' => '-74.1,40.7,-73.9,40.9',
                    'description' => 'Filter geometries that intersect with the given bounding box. '.
                        'Format is "minx,miny,maxx,maxy[,srid]" where SRID is optional and defaults to 4326 (WGS84).',
                ],
            ],
        ];
    }
}
