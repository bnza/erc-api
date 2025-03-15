<?php

namespace App\DTO\Import\CSV;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

class StratigraphicUnitDTO
{
    public mixed $id;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[AppAssert\PhpTypeStringRepresentation(type: 'string')]
    #[AppAssert\IsValidSiteCode]
    public mixed $site;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[AppAssert\PhpTypeStringRepresentation(type: 'int')]
    public mixed $year;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[AppAssert\PhpTypeStringRepresentation(type: 'int')]
    public mixed $number;

    #[Assert\Type('string')]
    #[AppAssert\PhpTypeStringRepresentation(type: 'string')]
    public mixed $interpretation;

    #[Assert\Type('string')]
    #[AppAssert\PhpTypeStringRepresentation(type: 'string')]
    public mixed $description;

    #[Assert\Type('string')]
    #[AppAssert\IsBooleanLike]
    public mixed $public = true;
}
