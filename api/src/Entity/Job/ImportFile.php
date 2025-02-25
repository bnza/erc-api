<?php

namespace App\Entity\Job;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;

class ImportFile
{

    private Uuid $id;

    public ?File $file = null;

    public string $filePath;
    public string $originalFilename;

    public string $sha256;
    public DateTimeImmutable $uploadDate;
    public string $mimeType;

    public int $size;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
