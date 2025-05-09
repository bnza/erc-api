<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Data\Site;
use App\Entity\Data\View\Geometry\VwSiteGeometry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;

final class SiteGeometryFilter extends AbstractFilter
{
    /**
     * @var FilterInterface[]
     */
    private array $siteFilters = [];

    public function __construct(
        #[TaggedLocator('api_platform.filter', indexAttribute: 'id')] ContainerInterface $filterLocator,
        ManagerRegistry $managerRegistry,
        ?array $properties = null,
    ) {
        parent::__construct($managerRegistry, $properties);

        // Extract only the Site filters from all available API Platform filters
        foreach ($filterLocator->getProvidedServices() as $id => $serviceId) {
            if (is_string($id) && str_starts_with($id, 'site.')) {
                $this->siteFilters[$id] = $filterLocator->get($id);
            }
        }
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
        // Only apply to VwSiteGeometry
        if (VwSiteGeometry::class !== $resourceClass) {
            return;
        }

        // Find a Site filter that can handle this property
        $matchingFilter = null;
        foreach ($this->siteFilters as $filterId => $filter) {
            $filterDescription = $filter->getDescription(Site::class);
            if (isset($filterDescription[$property])) {
                $matchingFilter = $filter;
                break;
            }
        }

        // If no matching filter found, don't apply anything
        if (!$matchingFilter) {
            return;
        }

        // Create a subquery to get Site IDs matching the filter criteria
        $entityManager = $this->managerRegistry->getManagerForClass(Site::class);
        $subQueryBuilder = $entityManager->createQueryBuilder();

        // Create a unique alias for the subquery
        $subqueryRootAlias = 'sub_site_filter_'.md5(uniqid('', true));

        $subQueryBuilder
            ->select("DISTINCT {$subqueryRootAlias}.id")
            ->from(Site::class, $subqueryRootAlias);

        // Create a separate query name generator for the subquery
        $subQueryNameGenerator = new QueryNameGenerator();

        // Apply the filter to the subquery
        $matchingFilter->filterProperty(
            $property,
            $value,
            $subQueryBuilder,
            $subQueryNameGenerator,
            Site::class,
            $operation,
            $context
        );

        // Apply the IN condition to the main query
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.id IN (%s)', $rootAlias, $subQueryBuilder->getDQL()));

        // Copy parameters from subquery to main query
        foreach ($subQueryBuilder->getParameters() as $parameter) {
            $queryBuilder->setParameter(
                $parameter->getName(),
                $parameter->getValue(),
                $parameter->getType()
            );
        }
    }

    public function getDescription(string $resourceClass): array
    {
        if (VwSiteGeometry::class !== $resourceClass) {
            return [];
        }

        $description = [];

        // Aggregate descriptions from all Site filters
        foreach ($this->siteFilters as $filter) {
            $filterDescription = $filter->getDescription(Site::class);
            $description = array_merge($description, $filterDescription);
        }

        return $description;
    }
}
