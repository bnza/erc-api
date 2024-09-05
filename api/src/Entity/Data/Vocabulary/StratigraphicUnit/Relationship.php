<?php

namespace App\Entity\Data\Vocabulary\StratigraphicUnit;

class Relationship
{

    public string $id;

    public string $value;
    public Relationship $invertedBy;

    public ?string $description;

//    public function getId(): string
//    {
//        return $this->id;
//    }
}
