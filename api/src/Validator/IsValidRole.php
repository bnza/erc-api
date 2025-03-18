<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class IsValidRole extends Constraint
{
    public string $message = 'Invalid role: {{  string }}';
}
