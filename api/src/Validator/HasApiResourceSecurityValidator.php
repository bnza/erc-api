<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class HasApiResourceSecurityValidator extends ConstraintValidator
{
    public function __construct(private Security $security, private EntityManagerInterface $dataEntityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, 'object');
        }

        $attribute = $this->dataEntityManager->contains($value) ? 'edit' : 'create';

        if (!$this->security->isGranted($attribute, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
