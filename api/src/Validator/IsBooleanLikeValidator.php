<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsBooleanLikeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsBooleanLike) {
            throw new UnexpectedTypeException($constraint, IsBooleanLike::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (is_bool($value)) {
            return;
        }

        if (0 === $value || 1 === $value) {
            return;
        }

        if (is_string($value) && in_array(strtolower($value), ['0', '1', 'true', 'false', 't', 'f'])) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', (string) $value)
            ->addViolation();
    }
}
