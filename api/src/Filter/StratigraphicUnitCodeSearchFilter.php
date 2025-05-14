<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class StratigraphicUnitCodeSearchFilter extends AbstractFilter
{
    private const string FILTER_KEY = 'search';

    // Possible filter patterns
    private const PATTERN_SITE_CODE = 'site_code';
    private const PATTERN_NUMERIC = 'numeric';
    private const PATTERN_SITE_AND_NUMERIC = 'site_and_numeric';
    private const PATTERN_SITE_YEAR_NUMBER = 'site_year_number';

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (self::FILTER_KEY !== $property) {
            return;
        }

        $values = $this->parseFilterValue($value);
        if (empty($values)) {
            return;
        }

        // Limit values to a maximum of 3 elements
        if (count($values) > 3) {
            $values = array_slice($values, 0, 3);
        }

        $pattern = $this->determineFilterPattern($values);

        $this->applyFilterByPattern(
            $pattern,
            $values,
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );
    }

    /**
     * Parse the filter value into component parts.
     */
    private function parseFilterValue($value): array
    {
        if (!is_string($value)) {
            return [];
        }

        $values = preg_split('/[\W\s]+/', $value);
        if (!is_array($values)) {
            return [];
        }

        return array_filter($values);
    }

    /**
     * Determine which filter pattern to use based on the values.
     */
    private function determineFilterPattern(array $values): string
    {
        $count = count($values);

        if (1 === $count) {
            return is_numeric($values[0])
                ? self::PATTERN_NUMERIC
                : self::PATTERN_SITE_CODE;
        }

        if (2 === $count) {
            return !is_numeric($values[0])
                ? self::PATTERN_SITE_AND_NUMERIC
                : self::PATTERN_NUMERIC; // Handle numeric.numeric as a special case
        }

        if (3 === $count && !is_numeric($values[0])) {
            return self::PATTERN_SITE_YEAR_NUMBER;
        }

        return ''; // Invalid pattern
    }

    /**
     * Apply the appropriate filter based on the determined pattern.
     */
    private function applyFilterByPattern(
        string $pattern,
        array $values,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        switch ($pattern) {
            case self::PATTERN_SITE_CODE:
                $this->applySiteCodeFilter($values[0], $queryBuilder, $queryNameGenerator, $resourceClass);
                break;

            case self::PATTERN_NUMERIC:
                if (1 === count($values)) {
                    $this->applyNumericFilter($values[0], $queryBuilder);
                } else {
                    $this->applyYearNumberFilter($values[0], $values[1], $queryBuilder);
                }
                break;

            case self::PATTERN_SITE_AND_NUMERIC:
                $this->applySiteAndNumericFilter(
                    $values[0],
                    $values[1],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass
                );
                break;

            case self::PATTERN_SITE_YEAR_NUMBER:
                $this->applySiteYearNumberFilter(
                    $values[0],
                    $values[1],
                    $values[2],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass
                );
                break;
        }
    }

    /**
     * Apply filter for [string] pattern - search for site code.
     */
    private function applySiteCodeFilter(
        string $siteCode,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        $expression = $this->getSiteFilterExpression($queryBuilder, $queryNameGenerator, $resourceClass);

        $queryBuilder
            ->andWhere($expression)
            ->setParameter('site_code', "%$siteCode%");
    }

    /**
     * Apply filter for [int] pattern - search for year or number.
     */
    private function applyNumericFilter(string $value, QueryBuilder $queryBuilder): void
    {
        $expressions = $this->createNumericFilterExpressions($queryBuilder);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(...$expressions))
            ->setParameter('search', "%$value");
    }

    /**
     * Apply filter for numeric.numeric pattern - search for both year and number.
     */
    private function applyYearNumberFilter(string $year, string $number, QueryBuilder $queryBuilder): void
    {
        $expressions = $this->createNumericFilterExpressions($queryBuilder, true);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->andX(...$expressions))
            ->setParameter('search0', "%$year")
            ->setParameter('search1', "%$number");
    }

    /**
     * Apply filter for [string].[int] pattern - search for site code and numeric.
     */
    private function applySiteAndNumericFilter(
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

        $numericExpressions = $this->createNumericFilterExpressions($queryBuilder);

        $queryBuilder
            ->andWhere($siteExpression)
            ->andWhere($queryBuilder->expr()->orX(...$numericExpressions))
            ->setParameter('site_code', "%$siteCode%")
            ->setParameter('search', "%$numeric");
    }

    /**
     * Apply filter for [string].[int].[int] pattern - search for site code, year, and number.
     */
    private function applySiteYearNumberFilter(
        string $siteCode,
        string $year,
        string $number,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        $numericExpressions = $this->createNumericFilterExpressions($queryBuilder, true);
        $siteExpression = $this->getSiteFilterExpression(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );

        $allExpressions = array_merge($numericExpressions, [$siteExpression]);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->andX(...$allExpressions))
            ->setParameter('site_code', "%$siteCode%")
            ->setParameter('search0', "%$year")
            ->setParameter('search1', "%$number");
    }

    /**
     * Create expressions for filtering on numeric fields (year and number).
     */
    private function createNumericFilterExpressions(
        QueryBuilder $queryBuilder,
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

    /**
     * Get expression for filtering on site code.
     */
    private function getSiteFilterExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): Comparison {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $format = '%s.%s';
        [$alias, $field] = $this->addJoinsForNestedProperty(
            'site.code',
            $rootAlias,
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass,
            Join::LEFT_JOIN
        );

        return $queryBuilder
            ->expr()
            ->like(
                sprintf($format, $alias, $field),
                $queryBuilder->expr()->upper(':site_code')
            );
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $property = implode(', ', array_keys($this->properties));
        $description[self::FILTER_KEY] = [
            'property' => $property,
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'description' => 'Filter stratigraphic unit by code. Supports formats: [site_code], [numeric], [site_code].[numeric], [site_code].[year].[number]. Additional values beyond 3 elements will be discarded.',
        ];

        return $description;
    }
}
