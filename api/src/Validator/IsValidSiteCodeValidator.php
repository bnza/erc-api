<?php

namespace App\Validator;

use App\Entity\Data\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsValidSiteCodeValidator extends ConstraintValidator
{
    private readonly SiteRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $repository = $this->entityManager->getRepository(Site::class);

        if (!$repository instanceof SiteRepository) {
            throw new UnexpectedTypeException($repository, SiteRepository::class);
        }

        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidSiteCode) {
            throw new UnexpectedTypeException($constraint, IsValidSiteCode::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!$this->repository->exists('code', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
