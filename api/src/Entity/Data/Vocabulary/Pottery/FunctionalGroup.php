<?php

namespace App\Entity\Data\Vocabulary\Pottery;

class FunctionalGroup
{
    private int $id;

    public string $value;

    public function getId(): int
    {
        return $this->id;
    }
}
