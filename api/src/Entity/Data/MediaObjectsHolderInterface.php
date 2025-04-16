<?php

namespace App\Entity\Data;

use App\Entity\Data\M2M\MediaObjectJoinInterface;
use Doctrine\Common\Collections\Collection;

interface MediaObjectsHolderInterface extends ApiIdentifiableEntityInterface
{
    public function getMediaObjects(): Collection;

    public function addMediaObject(MediaObjectJoinInterface $mediaObjectJoin): void;

    public function removeMediaObject(MediaObjectJoinInterface $mediaObjectJoin): void;
}
