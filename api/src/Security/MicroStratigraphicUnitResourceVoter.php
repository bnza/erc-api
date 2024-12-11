<?php

namespace App\Security;

use App\Entity\Data\MicroStratigraphicUnit;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroStratigraphicUnitResourceVoter extends Voter
{
    public const READ = 'read';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    private iterable $cache = [];

    public function __construct(
        readonly private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::READ, self::CREATE, self::DELETE, self::UPDATE])) {
            return false;
        }

        return is_object($subject)
            && in_array(get_class($subject), [
                MicroStratigraphicUnit::class,
            ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (self::READ === $attribute) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$this->security->isGranted('ROLE_GEO_ARCHAEOLOGIST')) {
            return false;
        }

        return $this->security->isGranted($attribute, $subject->sample);
    }
}
