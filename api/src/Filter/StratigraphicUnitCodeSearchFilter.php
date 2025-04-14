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

    protected function setSingleNumericFilterExpressions(
        QueryBuilder $queryBuilder,
        bool $distinct = false,
    ): array {
        $alias = $queryBuilder->getRootAliases()[0];
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

    protected function getSiteFilterExpression(
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

        $values = preg_split('/[\W\s]+/', $value);
        if (!is_array($values)) {
            return;
        }

        $values = array_filter($values);
        if (empty($values)) {
            return;
        }

        if (1 === count($values)) {
            $singleValue = $values[0];
            if (is_numeric($singleValue)) {
                $expressions = $this->setSingleNumericFilterExpressions($queryBuilder);
                $queryBuilder
                    ->andWhere(
                        $queryBuilder
                            ->expr()
                            ->orX(...$expressions)
                    )->setParameter('search', "%$singleValue");

                return;
            }

            $expression = $this->getSiteFilterExpression($queryBuilder, $queryNameGenerator, $resourceClass);

            $queryBuilder
                ->andWhere(
                    $expression
                )->setParameter('site_code', "%$singleValue%");

            return;
        }

        if (2 === count($values)) {
            $expressions = $this->setSingleNumericFilterExpressions($queryBuilder, is_numeric($values[0]));
            if (!is_numeric($values[0])) {
                $expression = $this->getSiteFilterExpression($queryBuilder, $queryNameGenerator, $resourceClass);

                $queryBuilder
                    ->andWhere($expression)
                    ->andWhere(
                        $queryBuilder
                            ->expr()
                            ->orX(...$expressions)
                    )
                    ->setParameter('site_code', "%$values[0]%")
                    ->setParameter('search', "%$values[1]");

                return;
            }

            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->andX(...$expressions)
                )
                ->setParameter('search0', "%$values[0]")
                ->setParameter('search1', "%$values[1]");

            return;
        }

        if (!is_numeric($values[0])) {
            return;
        }
        $expressions = $this->setSingleNumericFilterExpressions($queryBuilder, true);
        $expressions[] = $this->getSiteFilterExpression($queryBuilder, $queryNameGenerator, $resourceClass);

        $queryBuilder
            ->andWhere(
                $queryBuilder
                    ->expr()
                    ->andX(...$expressions)
            )
            ->setParameter('site_code', "%$values[0]%")
            ->setParameter('search0', "%$values[1]")
            ->setParameter('search1', "%$values[2]");
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
            'description' => 'Filter stratigraphic unit by code',
        ];

        return $description;
    }
}
