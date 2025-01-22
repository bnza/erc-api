<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\MediaObject;
use App\Entity\Data\Pottery;

class PotteriesMediaObject
{
    private $id;

    public Pottery $item;

    public MediaObject $mediaObject;

    public function getId(): int
    {
        return $this->id;
    }
}
