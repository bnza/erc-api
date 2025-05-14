<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

abstract class AbstractCodeSearchFilter extends AbstractFilter
{
    protected const string FILTER_KEY = 'search';

    // Common filter pattern constants
    protected const string PATTERN_SITE_CODE = 'site_code';
    protected const string PATTERN_NUMERIC = 'numeric';
    protected const string PATTERN_SITE_AND_NUMERIC = 'site_and_numeric';
    protected const string PATTERN_SITE_YEAR_NUMBER = 'site_year_number';
    protected const string PATTERN_MULTI_NUMERIC = 'multi_numeric';

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (static::FILTER_KEY !== $property) {
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
    protected function parseFilterValue($value): array
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
    protected function determineFilterPattern(array $values): string
    {
        $count = count($values);

        if (1 === $count) {
            return is_numeric($values[0])
                ? self::PATTERN_NUMERIC
                : self::PATTERN_SITE_CODE;
        }

        if (2 === $count) {
            if (!is_numeric($values[0])) {
                return self::PATTERN_SITE_AND_NUMERIC;
            }

            return self::PATTERN_MULTI_NUMERIC; // Handle numeric.numeric pattern
        }

        if (3 === $count && !is_numeric($values[0])) {
            return self::PATTERN_SITE_YEAR_NUMBER;
        }

        return ''; // Invalid pattern
    }

    /**
     * Apply the appropriate filter based on the determined pattern.
     */
    protected function applyFilterByPattern(
        string $pattern,
        array $values,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        switch ($pattern) {
            case self::PATTERN_SITE_CODE:
                $this->applySiteCodeFilter(
                    $values[0],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass
                );
                break;

            case self::PATTERN_NUMERIC:
                $this->applyNumericFilter($values[0], $queryBuilder, $queryNameGenerator, $resourceClass);
                break;

            case self::PATTERN_MULTI_NUMERIC:
                $this->applyMultiNumericFilter(
                    $values,
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass
                );
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
     * Get expression for filtering on site code.
     */
    protected function getSiteFilterExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): Comparison {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $format = '%s.%s';
        [$alias, $field] = $this->addJoinsForNestedProperty(
            $this->getSiteCodePath(),
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

    /**
     * Apply filter for [string] pattern - search for site code.
     */
    protected function applySiteCodeFilter(
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
     * Apply filter for [string].[int].[int] pattern - search for site code, year, and number.
     */
    protected function applySiteYearNumberFilter(
        string $siteCode,
        string $year,
        string $number,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        // Get site expression
        $siteExpression = $this->getSiteFilterExpression(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass
        );

        // Get numeric expressions with distinct parameters
        $numericExpressions = $this->createNumericFilterExpressions(
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass,
            true
        );

        // Combine all expressions
        $allExpressions = array_merge([$siteExpression], $numericExpressions);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->andX(...$allExpressions))
            ->setParameter('site_code', "%$siteCode%")
            ->setParameter('search0', "%$year%")
            ->setParameter('search1', "%$number%");
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $property = implode(', ', array_keys($this->properties));
        $description[static::FILTER_KEY] = [
            'property' => $property,
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'description' => $this->getFilterDescription(),
        ];

        return $description;
    }

    /**
     * Get the path to the site code field.
     */
    abstract protected function getSiteCodePath(): string;

    /**
     * Create expressions for filtering on numeric fields.
     */
    abstract protected function createNumericFilterExpressions(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        bool $distinct = false,
    ): array;

    /**
     * Apply filter for [int] pattern - search for numeric fields.
     */
    abstract protected function applyNumericFilter(
        string $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void;

    /**
     * Apply filter for [int].[int] pattern - search for multiple numeric fields.
     */
    abstract protected function applyMultiNumericFilter(
        array $values,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void;

    /**
     * Apply filter for [string].[int] pattern - search for site code and numeric.
     */
    abstract protected function applySiteAndNumericFilter(
        string $siteCode,
        string $numeric,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void;

    /**
     * Get the filter description to display in API documentation.
     */
    abstract protected function getFilterDescription(): string;
}
