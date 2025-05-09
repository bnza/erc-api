<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhpTypeStringRepresentationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PhpTypeStringRepresentation) {
            throw new UnexpectedTypeException($constraint, PhpTypeStringRepresentation::class);
        }

        if (null === $value || '' === $value) {
            return; // Allow null or empty values (adjust as needed).
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        switch ($constraint->type) {
            case 'int':
                if (false === filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $value)
                        ->setParameter('{{ type }}', 'integer')
                        ->addViolation();
                }
                break;
            case 'float':
                if (false === filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $value)
                        ->setParameter('{{ type }}', 'float')
                        ->addViolation();
                }
                break;
            case 'bool':
                if (!in_array(strtolower($value), ['0', '1', 'true', 'false', 't', 'f'])) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $value)
                        ->setParameter('{{ type }}', 'float')
                        ->addViolation();
                }
                break;
            case 'string':
                // Any string is valid.
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported type "%s".', $constraint->type));
        }
    }
}
