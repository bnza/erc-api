<?php

namespace App\Entity\Data;

use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateMediaObjectAction;
use ArrayObject;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            inputFormats: ['multipart' => ["multipart/form-data"]],
            controller: CreateMediaObjectAction::class,
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
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            validationContext: ['Default', 'MediaObject:create'],
            deserialize: false
        ),
    ],
    normalizationContext: ['groups' => ['MediaObject:read']]
)]
#[UniqueEntity(fields: ['sha256'], message: 'Duplicate media.')]
class MediaObject
{
    private int $id;

    #[Assert\NotBlank(groups: ['MediaObject:create'])]
    private ?File $file = null;

    public string $filePath;
    public string $originalFilename;

    #[Assert\NotBlank]
    public string $sha256;
    public DateTimeImmutable $uploadDate;
    private string $mimeType;

    public int $size;
    public ?string $contentUrl = null;
    private ?int $width = null;

    private ?int $height = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->sha256 = hash_file('sha256', $file);
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
