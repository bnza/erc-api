<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class IsMuInclusionsPercentageSum100 extends Constraint
{
    public string $message = 'Inclusions percentage sum is not correct.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
