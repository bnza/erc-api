<?php

namespace App\Tests\Functional\Api\Job\Import\Csv;

use App\Tests\Functional\Api\Job\Import\AbstractJobImportTest;

class JobImportCsvStratigraphicUnitTest extends AbstractJobImportTest
{
    public function testSuccess()
    {
        $uploadedFile = $this->getTestUploadFile('su.csv');
        $client = $this->createAuthenticatedClient();
        $response = $client->request(
            'POST',
            'api/jobs/import/csv/stratigraphic_unit',
            [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'extra' => [
                    'files' =>
                        [
                            'file' => $uploadedFile,
                        ],
                ],
            ],
        );
        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('jsonld');
        $jobId = $this->getJsonResponseId($response);

        $response = $client->request(
            'POST',
            "api/jobs/import/csv/stratigraphic_unit/$jobId/run",
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertResponseIsSuccessful();
        $this->assertEquals((string)$jobId, (string)$this->getJsonResponseId($response));

        $this->consumeQueue();

        $response = $client->request('GET', 'api/stratigraphic_units?site.code=ED&year=2025&number=1');
        $this->assertResponseIsSuccessful();

    }
}
