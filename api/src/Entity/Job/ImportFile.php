<?php

namespace App\Entity\Job;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateImportFileAction;
use ArrayObject;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;


#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            inputFormats: ['multipart' => ["multipart/form-data"]],
            controller: CreateImportFileAction::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            ),
            validationContext: ['Default', 'ImportFile:create'],
            deserialize: false
        ),
    ],
    normalizationContext: [
        'groups' => ['ImportFile:read'],
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class ImportFile
{
    private ?Uuid $id = null;

    public ?File $file = null;

    public ?string $filePath;
    public ?string $originalFilename;
    public ?string $contentUrl = null;

    public DateTimeImmutable $uploadDate;
    public ?string $mimeType;

    public ?int $size;
    public ?string $description = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function __construct(Uuid|string|null $id = null)
    {
        if (is_null($id)) {
            return;
        }
        $this->id = is_string($id) ? Uuid::fromString($id) : $id;
    }

    public function getUploadDate(): DateTimeImmutable
    {
        return $this->uploadDate;
    }

    public function setUploadDate(DateTimeImmutable $uploadDate): ImportFile
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): ImportFile
    {
        $this->file = $file;

        return $this;
    }

    public function setDescription(?string $description): ImportFile
    {
        $this->description = $description;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): ImportFile
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(?string $originalFilename): ImportFile
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): ImportFile
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): ImportFile
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): ImportFile
    {
        $this->size = $size;

        return $this;
    }

    public function prePersist(): void
    {
        if ($this->id === null) {
            $this->id = Uuid::v4();
        }
    }
}
