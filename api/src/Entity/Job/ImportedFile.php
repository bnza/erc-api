<?php

namespace App\Entity\Job;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Entity\Data\MediaObject;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(parameters: [
            'order[:property]' => new QueryParameter(filter: 'imported_file.order_filter'),
        ]),
    ],
    normalizationContext: [
        'groups' => ['ImportedFile:read'],
    ],
    security: "is_granted('ROLE_EDITOR')"
)]
class ImportedFile
{
    public Uuid $id;

    public MediaObject $mediaObject;

    public string $userId;

    public string $service;

    public DateTimeImmutable $uploadDate;

    public function setId(Uuid $id): ImportedFile
    {
        $this->id = $id;

        return $this;
    }

    public function setMediaObject(MediaObject $mediaObject): ImportedFile
    {
        $this->mediaObject = $mediaObject;

        return $this;
    }

    public function setUserId(string $userId): ImportedFile
    {
        $this->userId = $userId;

        return $this;
    }

    public function setService(string $service): ImportedFile
    {
        $this->service = $service;

        return $this;
    }

    public function setUploadDate(DateTimeImmutable $uploadDate): ImportedFile
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

}
