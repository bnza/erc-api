<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Data\StratigraphicUnit;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class PublicStratigraphicUnitExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass): void
    {
        if (
            StratigraphicUnit::class !== $resourceClass
            || $this->security->isGranted('IS_AUTHENTICATED_FULLY')
        ) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $joinSite = sprintf('%s.site', $rootAlias);
        $joinSiteAlias = $queryNameGenerator->generateJoinAlias('site');
        $queryBuilder
            ->innerJoin(
                $joinSite,
                $joinSiteAlias,
                Expr\Join::WITH,
                $queryBuilder->expr()->eq($rootAlias.'.site', $joinSiteAlias.'.id'))
            ->andWhere(sprintf('%s.public = true', $joinSiteAlias))
            ->andWhere(sprintf('%s.public = true', $rootAlias));
    }
}
