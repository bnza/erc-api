<?php

namespace App\Security;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkUnitRunResourceVoter extends Voter
{
    public const string RUN = 'run';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::RUN && $subject instanceof WorkUnitEntity;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userId = $token->getUser()->getUserIdentifier();
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /* @var WorkUnitEntity $subject */
        return $subject->getUserId() === $userId;
    }
}
