<?php

namespace App\Repository;

use App\Entity\Data\User;
use App\Security\ApiPrivileges;
use App\Security\PrivilegeValueOperator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, CheckUniqueRepositoryInterface
{
    use AbstractCheckUniqueRepositoryTrait;

    public function __construct(ManagerRegistry $registry, private PrivilegeValueOperator $privilegeValueOperator)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    protected function getUniqueFields(): array
    {
        return ['email'];
    }

    public function hasSitePrivilege(User $user, int $siteId, ApiPrivileges $privilege = ApiPrivileges::Editor): bool
    {
        $qb = $this->createQueryBuilder('u');
        $and = $qb->expr()->andX();
        $and->addMultiple([
            $qb->expr()->eq('u.id', ':userId'),
            $qb->expr()->eq('su.site', ':siteId'),
        ]);
        $qb->select('su.privileges')
            ->innerJoin(
                'App\Entity\Data\M2M\SitesUsers',
                'su',
                Expr\Join::WITH,
                $qb->expr()->eq('u.id', 'su.user'),
            )
            ->where($and)
            ->setParameters([
                ':userId' => $user->getId(),
                ':siteId' => $siteId,
            ]);
        try {
            $privileges = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }

        return $this->privilegeValueOperator->hasPrivilege($privileges, $privilege);
    }
}
