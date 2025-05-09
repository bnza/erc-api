<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\MediaObject;
use App\Entity\Data\MediaObjectsHolderInterface;

interface MediaObjectJoinInterface
{
    public function getItemClass(): string;

    public function setItem(MediaObjectsHolderInterface $item): void;

    public function setMediaObject(MediaObject $mediaObject): void;
}
