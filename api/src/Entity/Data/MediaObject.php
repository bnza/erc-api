<?php

namespace App\Entity\Data;

use Symfony\Component\HttpFoundation\File\File;

class MediaObject
{
    private int $id;

    public ?File $file = null;

    public string $filePath;
    public string $originalFilename;

    public string $sha256;
    public \DateTimeImmutable $uploadDate;
    private string $mimeType;

    public int $size;
    public ?string $contentUrl = null;
    private ?int $width = null;

    private ?int $height = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = 'image/jpg' === $mimeType ? 'image/jpeg' : $mimeType;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getDimensions(): ?array
    {
        return $this->width ? [$this->width, $this->height] : null;
    }

    public function setDimensions(?array $dimensions): MediaObject
    {
        $this->width = $dimensions[0];
        $this->height = $dimensions[1];

        return $this;
    }
}
