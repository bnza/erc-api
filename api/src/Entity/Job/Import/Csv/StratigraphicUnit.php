<?php

namespace App\Entity\Job\Import\Csv;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\State\Job\Import\FileBasedImportProcessor;
use ArrayObject;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'ImportCsv',
    operations: [
        new Post(
            uriTemplate: 'work_units/import/csv/stratigraphic_units',
            inputFormats: ['multipart' => ["multipart/form-data"]],
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
            denormalizationContext: [FileBasedImportProcessor::CONTEXT_KEY => 'app.job.import.csv.stratigraphic_unit'],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            processor: FileBasedImportProcessor::class
        ),
    ],
)]
#[Vich\Uploadable]
class StratigraphicUnit
{
    #[Vich\UploadableField(
        mapping: 'import_file',
        fileNameProperty: 'filePath',
        size: 'size',
        mimeType: 'mimeType',
        originalName: 'originalFilename')]
    public ?File $file;

    public ?string $description = null;
}
