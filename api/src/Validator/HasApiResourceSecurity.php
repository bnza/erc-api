<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class HasApiResourceSecurity extends Constraint
{
    public string $message = 'You don\'t have permission to add/modify this resource';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT; // or self::PROPERTY_CONSTRAINT | self::METHOD_CONSTRAINT | self::CLASS_CONSTRAINT
    }
}
