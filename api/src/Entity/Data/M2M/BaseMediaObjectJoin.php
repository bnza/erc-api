<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\MediaObject;
use App\Entity\Data\MediaObjectsHolderInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['item', 'mediaObject'],
    message: 'Duplicate item - media object pair'
)]
abstract class BaseMediaObjectJoin
{

    #[Groups(['MediaObjectJoin:read', 'MediaObjectJoin:create'])]
    private $id;

    #[Assert\NotNull]
    #[Groups(['MediaObjectJoin:read', 'MediaObjectJoin:create'])]
    protected MediaObject $mediaObject;

    #[Assert\NotNull]
    #[Groups(['MediaObjectJoin:read', 'MediaObjectJoin:create'])]
    protected MediaObjectsHolderInterface $item;

    #[Groups(['MediaObjectJoin:read', 'MediaObjectJoin:create'])]
    protected ?string $description;

    abstract function getItemClass(): string;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMediaObject(): MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(MediaObject $mediaObject): void
    {
        $this->mediaObject = $mediaObject;
    }

    public function getItem(): MediaObjectsHolderInterface
    {
        return $this->item;
    }

    public function setItem(MediaObjectsHolderInterface $item): void
    {
        if (get_class($item) !== $this->getItemClass()) {
            throw new InvalidArgumentException(
                sprintf('Item class should be %s, %s giver', $this->getItemClass(), get_class($item))
            );
        }
        $this->item = $item;
    }
}
