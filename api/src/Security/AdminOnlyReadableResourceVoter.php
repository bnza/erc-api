<?php

namespace App\Security;

use App\Entity\Data\M2M\SitesUsers;
use App\Entity\Data\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminOnlyReadableResourceVoter extends Voter
{
    public const READ = 'read';

    private readonly array $supportedClasses;

    public function __construct(private Security $security)
    {
        $this->supportedClasses = [
            User::class,
            SitesUsers::class,
        ];
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::READ !== $attribute) {
            return false;
        }

        return in_array(get_class($subject), $this->supportedClasses, true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
