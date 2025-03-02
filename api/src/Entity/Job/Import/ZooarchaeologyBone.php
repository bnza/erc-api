<?php

namespace App\Entity\Job\Import;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Service\Job\Import\ZooarchaeologyBoneImportJob;
use App\State\Job\Import\FileBasedImportRunnerProcessor;
use Symfony\Component\HttpFoundation\File\File;
use App\State\Job\Import\FileBasedImportProcessor;
use ArrayObject;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'Import',
    operations: [
        new Post(
            uriTemplate: 'api/jobs/import/zooarchaeology/bones',
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
            denormalizationContext: [FileBasedImportProcessor::CONTEXT_KEY => 'app.job.import.zooarchaeology_bone'],
            processor: FileBasedImportProcessor::class
        ),
        new Post(
            uriTemplate: 'api/jobs/import/zooarchaeology/bones/{id}/run',
            denormalizationContext: [FileBasedImportProcessor::CONTEXT_KEY => 'app.job.import.zooarchaeology_bone'],
            processor: FileBasedImportRunnerProcessor::class
        ),
    ],
)]
#[Vich\Uploadable]
class ZooarchaeologyBone
{
    #[Vich\UploadableField(
        mapping: 'import_file',
        fileNameProperty: 'filePath',
        size: 'size',
        mimeType: 'mimeType',
        originalName: 'originalFilename')]
    public ?File $file;

    public readonly string $jobClassName;

    public function __construct()
    {
        $this->jobClassName = ZooarchaeologyBoneImportJob::class;
    }
}
