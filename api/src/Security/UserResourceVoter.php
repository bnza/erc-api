<?php

namespace App\Security;

use App\Entity\Data\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserResourceVoter extends Voter
{
    public const CREATE = 'create';
    public const READ = 'read';
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

        return $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return false;
        }

        return self::READ === $attribute || $subject->email !== $token->getUserIdentifier();
    }
}
