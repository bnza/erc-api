<?php

namespace App\Repository;

use App\Entity\Data\M2M\SitesUsers;
use App\Entity\Data\User;
use App\Security\ApiPrivileges;
use App\Security\PrivilegeValueOperator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

class SitesUsersRepository extends AbstractCheckUniqueRepository
{

    private PrivilegeValueOperator $privilegeValueOperator;

    public function __construct(
        EntityManagerInterface $registry,
    ) {
        parent::__construct($registry, new ClassMetadata(SitesUsers::class));
    }

    public function setPrivilegeOperator(PrivilegeValueOperator $privilegeValueOperator): void
    {
        $this->privilegeValueOperator = $privilegeValueOperator;
    }

    protected function getUniqueFields(): array
    {
        return [['site', 'user']];
    }

    public function getUserSitePrivilege(
        User $user,
        int $siteId,
        ApiPrivileges $privilege = ApiPrivileges::Editor
    ): int|bool {
        $qb = $this->createQueryBuilder('su');
        $and = $qb->expr()->andX();
        $and->addMultiple([
            $qb->expr()->eq('su.user', ':userId'),
            $qb->expr()->eq('su.site', ':siteId'),
        ]);
        $qb->select(['su.privileges'])
            ->where($and)
            ->setParameters(
                new ArrayCollection([
                    new Parameter(':userId', $user->getId()),
                    new Parameter(':siteId', $siteId),
                ])
            );
        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    public function hasSitePrivilege(
        string $userId,
        int $siteId,
        ?ApiPrivileges $privilege = null
    ): bool {
        $qb = $this->createQueryBuilder('su');
        $and = $qb->expr()->andX();
        $and->addMultiple([
            $qb->expr()->eq('su.user', ':userId'),
            $qb->expr()->eq('su.site', ':siteId'),
        ]);
        $qb->select(['su.privileges'])
            ->where($and)
            ->setParameters(
                new ArrayCollection([
                    new Parameter(':userId', $userId),
                    new Parameter(':siteId', $siteId),
                ])
            );
        try {
            $privileges = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }

        return is_null($privilege) || $this->privilegeValueOperator->hasPrivilege($privileges, $privilege);
    }
}
