<?php

namespace App\Entity\Job;

use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateImportFileAction;
use App\Controller\CreateMediaObjectAction;
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
    normalizationContext: ['groups' => ['MediaObject:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class ImportFile
{

    private Uuid $id;

    public ?File $file = null;

    public string $filePath;
    public string $originalFilename;

    public DateTimeImmutable $uploadDate;
    public string $mimeType;

    public int $size;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
