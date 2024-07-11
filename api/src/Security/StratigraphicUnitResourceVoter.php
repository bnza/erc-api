<?php

namespace App\Security;

use App\Entity\Data\StratigraphicUnit;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StratigraphicUnitResourceVoter extends Voter
{
    public const READ = 'read';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::READ, self::CREATE, self::DELETE, self::UPDATE])) {
            return false;
        }

        return $subject instanceof StratigraphicUnit;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (self::CREATE === $attribute) {
            return $this->security->isGranted('IS_AUTHENTICATED_FULLY');
        }

        if (self::UPDATE === $attribute) {
            return $this->security->isGranted('ROLE_EDITOR');
        }

        if (self::DELETE === $attribute) {
            return $this->security->isGranted('ROLE_EDITOR');
        }

        return true;
    }
}
