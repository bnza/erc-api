<?php

namespace App\Entity\Job\Import\Csv;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

class AbstractCsvFileImportedEntity implements CsvFileImportedEntityInterface
{
    #[Vich\UploadableField(
        mapping: 'import_file',
        fileNameProperty: 'filePath',
        size: 'size',
        mimeType: 'mimeType',
        originalName: 'originalFilename')]
    protected ?File $file;

    protected ?string $description = null;

    #[\Override]
    public function getFile(): ?File
    {
        return $this->file;
    }

    #[\Override]
    public function setFile(?File $file): AbstractCsvFileImportedEntity
    {
        $this->file = $file;

        return $this;
    }

    #[\Override]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[\Override]
    public function setDescription(?string $description): AbstractCsvFileImportedEntity
    {
        $this->description = $description;

        return $this;
    }
}
