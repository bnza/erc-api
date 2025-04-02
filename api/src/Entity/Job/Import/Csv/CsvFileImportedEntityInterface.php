<?php

namespace App\Entity\Job\Import\Csv;

use Symfony\Component\HttpFoundation\File\File;

interface CsvFileImportedEntityInterface
{
    public function getFile(): ?File;

    public function setFile(File $file): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;
}
