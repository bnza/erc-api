<?php

namespace App\Entity\Data;

use App\Entity\Data\M2M\MediaObjectJoinInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait MediaObjectsHolderTrait
{
    private ?Collection $mediaObjects = null;

    public function getMediaObjects(): Collection
    {
        return $this->mediaObjects ??= new ArrayCollection();
    }

    public function addMediaObject(MediaObjectJoinInterface $mediaObject): void
    {
        $this->getMediaObjects()->add($mediaObject);
        $mediaObject->setItem($this);
    }

    public function removeMediaObject(MediaObjectJoinInterface $mediaObject): void
    {
        $this->getMediaObjects()->removeElement($mediaObject);
    }
}
