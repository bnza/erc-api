<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final class SampleCodeSearchFilter extends AbstractCodeSearchFilter
{
    protected function getSiteCodePath(): string
    {
        return 'stratigraphicUnit.site.code';
    }

    protected function createNumericFilterExpressions(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        bool $distinct = false,
    ): array {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $expressions = [];

        // Add expression for stratigraphicUnit.year
        [$yearAlias, $yearField] = $this->addJoinsForNestedProperty(
            'stratigraphicUnit.year',
            $rootAlias,
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass,
            Join::LEFT_JOIN
        );
        $expressions[] = $queryBuilder->expr()->like(
            sprintf('CAST(%s.%s AS string)', $yearAlias, $yearField),
            $distinct ? ':search0' : ':search'
        );

        // Add expression for number (direct field)
        $expressions[] = $queryBuilder->expr()->like(
            sprintf('CAST(%s.%s AS string)', $rootAlias, 'number'),
            $distinct ? ':search1' : ':search'
        );

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
        return 'Filter samples by various criteria. Supports formats: '.
            '[site_code] - filters by site code, '.
            '[numeric] - filters by stratigraphic unit year or sample number, '.
            '[site_code].[numeric] - filters by site code AND year/number, '.
            '[site_code].[year].[number] - filters by specific site code, stratigraphic unit year, and sample number. '.
            'Additional values beyond 3 elements will be discarded.';
    }
}
