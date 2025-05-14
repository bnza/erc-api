<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class StratigraphicUnitCodeSearchFilter extends AbstractCodeSearchFilter
{
    protected function getSiteCodePath(): string
    {
        return 'site.code';
    }

    protected function createNumericFilterExpressions(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        bool $distinct = false,
    ): array {
        $alias = $queryBuilder->getRootAliases()[0];
        $expressions = [];

        foreach (['year', 'number'] as $i => $field) {
            $format = 'CAST(%s.%s AS string)';
            $expressions[] = $queryBuilder
                ->expr()
                ->like(
                    sprintf($format, $alias, $field),
                    $distinct ? ":search$i" : ':search'
                );
        }

        return $expressions;
    }

    protected function applyNumericFilter(
        string $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        $expressions = $this->createNumericFilterExpressions(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(...$expressions))
            ->setParameter('search', "%$value%");
    }

    protected function applyMultiNumericFilter(
        array $values,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        $expressions = $this->createNumericFilterExpressions(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass,
            true
        );

        $queryBuilder
            ->andWhere($queryBuilder->expr()->andX(...$expressions))
            ->setParameter('search0', "%{$values[0]}%")
            ->setParameter('search1', "%{$values[1]}%");
    }

    protected function applySiteAndNumericFilter(
        string $siteCode,
        string $numeric,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        $siteExpression = $this->getSiteFilterExpression(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );

        $numericExpressions = $this->createNumericFilterExpressions(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );

        $queryBuilder
            ->andWhere($siteExpression)
            ->andWhere($queryBuilder->expr()->orX(...$numericExpressions))
            ->setParameter('site_code', "%$siteCode%")
            ->setParameter('search', "%$numeric%");
    }

    protected function getFilterDescription(): string
    {
        return 'Filter stratigraphic unit by code. Supports formats: [site_code], [numeric], '.
            '[site_code].[numeric], [site_code].[year].[number]. Additional values beyond 3 elements will be discarded.';
    }
}
