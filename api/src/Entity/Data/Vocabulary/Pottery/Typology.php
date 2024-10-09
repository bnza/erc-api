<?php

namespace App\Entity\Data\Vocabulary\Pottery;

class Typology
{

    private int $id;

    public string $value;

    public function getId(): int
    {
        return $this->id;
    }
}
