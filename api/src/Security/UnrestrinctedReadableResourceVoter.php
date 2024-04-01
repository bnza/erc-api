<?php

namespace App\Security;

use App\Entity\Data\Area;
use App\Entity\Data\Site;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UnrestrinctedReadableResourceVoter extends Voter
{
    public const READ = 'read';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::READ !== $attribute) {
            return false;
        }

        return is_object($subject)
            && in_array(get_class($subject), [
                Site::class,
                Area::class,
            ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
