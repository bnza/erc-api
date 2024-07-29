<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class OrSearchFilter extends AbstractFilter
{
    private const FILTER_KEY = 'search';

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (self::FILTER_KEY !== $property) {
            return;
        }

        $expressions = [];
        foreach ($this->getProperties() as $property => $stat) {
            if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass, true)) {
                return;
            }

            $alias = $queryBuilder->getRootAliases()[0];
            $field = $property;

            if ($this->isPropertyNested($property, $resourceClass)) {
                [$alias, $field] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass, Join::LEFT_JOIN);
            }

            $expressions[] = $queryBuilder
                ->expr()
                ->like(
                    sprintf('LOWER(%s.%s)', $alias, $field),
                    $queryBuilder->expr()->lower(':search')
                );
        }
        $queryBuilder
            ->andWhere(
                $queryBuilder
                    ->expr()
                    ->orX(...$expressions)
            )->setParameter('search', "%$value%");
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
            'description' => 'Filter by insensitive LIKE "'.$property.'" using OR condition',
        ];

        return $description;
    }
}
