<?php

namespace App\Security;

use App\Entity\Data\M2M\PotteriesMediaObject;
use App\Entity\Data\M2M\StratigraphicUnitsMediaObject;
use App\Entity\Data\Pottery;
use App\Entity\Data\Sample;
use App\Entity\Data\StratigraphicUnit;
use App\Entity\Data\View\M2M\VwStratigraphicUnitsRelationship;
use App\Repository\SitesUsersRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StratigraphicUnitResourceVoter extends Voter
{
    public const READ = 'read';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    private iterable $cache = [];

    public function __construct(
        readonly private Security $security,
        readonly private SitesUsersRepository $sitesUsersRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::READ, self::CREATE, self::DELETE, self::UPDATE])) {
            return false;
        }

        return is_object($subject)
            && in_array(get_class($subject), [
                PotteriesMediaObject::class,
                Pottery::class,
                Sample::class,
                StratigraphicUnit::class,
                VwStratigraphicUnitsRelationship::class,
                StratigraphicUnitsMediaObject::class,
            ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (self::READ === $attribute) {
            return true;
        }

        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $isStratigraphicUnit = $subject instanceof StratigraphicUnit;

        if (in_array(get_class($subject), [Pottery::class, Sample::class])) {
            $subject = $subject->stratigraphicUnit;
        }

        if ($subject instanceof VwStratigraphicUnitsRelationship) {
            $subject = $subject->sxSU;
        }

        if ($subject instanceof StratigraphicUnitsMediaObject) {
            $subject = $subject->item;
        }

        if ($subject instanceof PotteriesMediaObject) {
            $subject = $subject->item->stratigraphicUnit;
        }

        $userId = $this->security->getUser()->getId()->__toString();
        $siteId = $subject->site->getId();
        $key = "$siteId/$userId";

        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->sitesUsersRepository->hasSitePrivilege(
                $userId,
                $siteId,
            );
        }
        $hasSiteRoleBase = $this->cache[$key];

        if (self::CREATE === $attribute) {
            return $isStratigraphicUnit || $hasSiteRoleBase;
        }

        if (self::UPDATE === $attribute) {
            return $hasSiteRoleBase;
        }

        if (self::DELETE === $attribute) {
            return $hasSiteRoleBase;
        }

        return false;
    }
}
