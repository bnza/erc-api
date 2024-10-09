<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class IsLessThanOrEqualToCurrentYear extends Constraint
{
    public string $message = 'Year must be lower than or equal to current year: {{  string }}';
}
