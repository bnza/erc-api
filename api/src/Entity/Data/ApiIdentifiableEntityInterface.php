<?php

namespace App\Entity\Data;

use Symfony\Component\Uid\Uuid;

interface ApiIdentifiableEntityInterface
{
    public function getId(): int|Uuid;
}
