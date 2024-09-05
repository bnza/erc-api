<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class StratigraphicUnitCodeSearchFilter extends AbstractFilter
{
    private const FILTER_KEY = 'search';

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

        //        if (
        //            !$this->isPropertyEnabled($property, $resourceClass) ||
        //            !$this->isPropertyMapped($property, $resourceClass)
        //        ) {
        //            return;
        //        }

        $values = preg_split('/[\W\s]+/', $value);
        if (!is_array($values)) {
            return;
        }

        $expressions = [];

        if (1 === count($values)) {
            $alias = $queryBuilder->getRootAliases()[0];
            if (is_numeric($values[0])) {
                foreach (['year', 'number'] as $field) {
                    $format = 'CAST(%s.%s AS string)';
                    $expressions[] = $queryBuilder
                        ->expr()
                        ->like(
                            sprintf($format, $alias, $field),
                            ':search'
                        );
                }
                $queryBuilder
                    ->andWhere(
                        $queryBuilder
                            ->expr()
                            ->orX(...$expressions)
                    )->setParameter('search', "%$values[0]");

                return;
            }
            $format = '%s.%s';
            [$alias, $field] = $this->addJoinsForNestedProperty(
                'site.code',
                $alias,
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                Join::LEFT_JOIN
            );
            $expression = $queryBuilder
                ->expr()
                ->like(
                    sprintf($format, $alias, $field),
                    $queryBuilder->expr()->upper(':search')
                );
            $queryBuilder
                ->andWhere(
                    $expression
                )->setParameter('search', "%$value%");

            return;
        }

        if (2 === count($values)) {
            if (!is_numeric($values[0])) {
                [$alias, $field] = $this->addJoinsForNestedProperty(
                    'site.code',
                    $queryBuilder->getRootAliases()[0],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    Join::LEFT_JOIN
                );
                $expression = $queryBuilder
                    ->expr()
                    ->like(
                        sprintf('%s.%s', $alias, $field),
                        $queryBuilder->expr()->upper(':site_code')
                    );
                foreach (['year', 'number'] as $field) {
                    $format = 'CAST(%s.%s AS string)';
                    $alias = $queryBuilder->getRootAliases()[0];
                    $expressions[] = $queryBuilder
                        ->expr()
                        ->like(
                            sprintf($format, $alias, $field),
                            ':search'
                        );
                }
                $queryBuilder
                    ->andWhere($expression)
                    ->andWhere(
                        $queryBuilder
                            ->expr()
                            ->orX(...$expressions)
                    )
                    ->setParameter('site_code', "%$values[0]%")
                    ->setParameter('search', "%$values[1]%");

                return;
            }
            foreach (['year', 'number'] as $i => $field) {
                $format = 'CAST(%s.%s AS string)';
                $alias = $queryBuilder->getRootAliases()[0];
                $expressions[] = $queryBuilder
                    ->expr()
                    ->like(
                        sprintf($format, $alias, $field),
                        ":search$i"
                    );
            }
            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->andX(...$expressions)
                )
                ->setParameter('search0', "%$values[0]%")
                ->setParameter('search1', "%$values[1]%");

            return;
        }

        if (3 >= count($values)) {
            if (!is_numeric($values[0])) {
                [$alias, $field] = $this->addJoinsForNestedProperty(
                    'site.code',
                    $queryBuilder->getRootAliases()[0],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    Join::LEFT_JOIN
                );
                $expressions[] = $queryBuilder
                    ->expr()
                    ->like(
                        sprintf('%s.%s', $alias, $field),
                        $queryBuilder->expr()->upper(':site_code')
                    );
            }
            foreach (['year', 'number'] as $i => $field) {
                $format = 'CAST(%s.%s AS string)';
                $alias = $queryBuilder->getRootAliases()[0];

                $expressions[] = $queryBuilder
                    ->expr()
                    ->like(
                        sprintf($format, $alias, $field),
                        $queryBuilder->expr()->upper(":search$i")
                    );
            }
            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->andX(...$expressions)
                )
                ->setParameter('search0', "%$values[1]")
                ->setParameter('search1', "%$values[2]");

            if (!is_numeric($values[0])) {
                $queryBuilder->setParameter('site_code', "%$values[0]%");
            }

            return;
        }
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
