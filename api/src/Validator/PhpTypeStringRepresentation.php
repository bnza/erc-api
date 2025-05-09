<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PhpTypeStringRepresentation extends Constraint
{
    public string $message = 'The value "{{ value }}" is not a valid {{ type }} string representation.';
    public string $type = 'string'; // Default type
    public array $allowedTypes = ['string', 'int', 'float', 'bool'];

    public function __construct(?string $type, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        if ($type) {
            $this->type = $type;
        }
    }
}
