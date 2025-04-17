<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class IsResourceNotReferenced extends Constraint
{
    public string $message = 'Cannot delete - resource is still referenced in {{ entity }} ({{ count }}x)';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
