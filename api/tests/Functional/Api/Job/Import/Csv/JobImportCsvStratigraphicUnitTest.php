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

    public function testHeadersValidationError()
    {
        $client = $this->createAuthenticatedClient();
        $url = 'api/jobs/import/csv/stratigraphic_unit';
        $jobId = $this->uploadFile($client, 'su__headers_err.csv', $url);

        $this->runJob($client, $jobId, $url);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testDataValidationError()
    {
        $client = $this->createAuthenticatedClient();
        $url = 'api/jobs/import/csv/stratigraphic_unit';
        $jobId = $this->uploadFile($client, 'su__err1.csv', $url);

        $this->runJob($client, $jobId, $url);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testWrongFileMimeTypeError()
    {
        $client = $this->createAuthenticatedClient();
        $url = 'api/jobs/import/csv/stratigraphic_unit';
        $jobId = $this->uploadFile($client, 'test.pdf', $url);

        $this->runJob($client, $jobId, $url);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testFakeCsvFileError()
    {
        $client = $this->createAuthenticatedClient();
        $url = 'api/jobs/import/csv/stratigraphic_unit';
        $jobId = $this->uploadFile($client, 'fake.csv', $url);

        $this->runJob($client, $jobId, $url);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnit($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }
}
