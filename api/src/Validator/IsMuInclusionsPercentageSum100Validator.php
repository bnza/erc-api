<?php

namespace App\Validator;

use App\Entity\Data\MicroStratigraphicUnit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsMuInclusionsPercentageSum100Validator extends ConstraintValidator
{
    /**
     * @param MicroStratigraphicUnit $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof MicroStratigraphicUnit) {
            throw new UnexpectedValueException($value, MicroStratigraphicUnit::class);
        }

        $sum = $value->inclusionsBuildingMaterials + $value->inclusionsGeology + $value->inclusionsDomesticRefuse + $value->inclusionsOrganicRefuse;

        if (100 !== $sum) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('inclusionsGeology')
                ->addViolation();
        }
    }
}
