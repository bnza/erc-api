<?php

namespace App\Tests\Functional\Api\Job\Import\Csv;

use App\Tests\Functional\Api\Job\Import\AbstractJobImportTest;

class JobImportCsvStratigraphicUnitTest extends AbstractJobImportTest
{
    public function testSuccess()
    {
        $client = $this->createAuthenticatedClient();
        $url = 'api/jobs/import/csv/stratigraphic_unit';

        $jobId = $this->uploadFile($client, 'su.csv', $url);

        $this->runJob($client, $jobId, $url);

        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(0, $status['value']);

        $this->consumeQueue();
        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(2, $status['value']);

        $client->request('GET', 'api/stratigraphic_units?site.code=ED&year=2025&number=1');
        $this->assertResponseIsSuccessful();
    }
}
