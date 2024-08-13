<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Data\Site;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class AuthorizedSiteExtension implements QueryCollectionExtensionInterface
{
    private const OPERATION_NAME = '_api_/autocomplete/sites/authorized_get_collection';

    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (
            Site::class !== $resourceClass
            || !$this->security->isGranted('IS_AUTHENTICATED_FULLY')
            || $this->security->isGranted('ROLE_ADMIN')
        ) {
            return;
        }
        if (self::OPERATION_NAME !== $context['operation_name']) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $joinAlias = $queryNameGenerator->generateJoinAlias('su');
        $queryBuilder->leftJoin(
            'App\Entity\Data\M2M\SitesUsers',
            $joinAlias,
            Expr\Join::WITH,
            $queryBuilder->expr()->eq(
                sprintf('%s.id', $rootAlias),
                sprintf('%s.site', $joinAlias)
            )
        );
        $paramName = $queryNameGenerator->generateParameterName(':user');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                sprintf('%s.user', $joinAlias),
                $paramName
            )
        );
        $queryBuilder->setParameter(
            $paramName,
            $this->security->getUser()->getId()
        );
    }
}
