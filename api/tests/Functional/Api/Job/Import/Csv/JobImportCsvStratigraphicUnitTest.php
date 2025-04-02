<?php

namespace App\Tests\Functional\Api\Job\Import\Csv;

use App\Tests\Functional\Api\Job\Import\JobImportTestCase;
use Symfony\Component\HttpFoundation\Response;

class JobImportCsvStratigraphicUnitTest extends JobImportTestCase
{
    private const string URL = 'api/work_units/import/csv/stratigraphic_units';

    public function testSingleSuccess()
    {
        $clientEditor = $this->createAuthenticatedClient(self::USER_EDITOR, self::USER_EDITOR_PW);
        $client = $this->createAuthenticatedClient();

        $jobId = $this->uploadFile($client, 'su.csv', self::URL, null, 'Some description');

        $this->runJob($client, $jobId, self::URL);

        $response = $this->fetchApiWorkUnitItem($clientEditor, $jobId);
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(0, $status['value']);

        $this->consumeQueue();
        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(2, $status['value']);

        $response = $this->fetchApiWorkUnitCollection($client);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(2, $response->toArray()['totalItems']);

        $response = $client->request('GET', 'api/stratigraphic_units?site.code=ED&year=2025&number=1');
        $this->assertResponseIsSuccessful();
    }

    public function testTwoSuccess()
    {

        $client = $this->createAuthenticatedClient();
        $jobId1 = $this->uploadFile($client, 'su.csv', self::URL);

        $this->runJob($client, $jobId1, self::URL);
        $response = $this->fetchApiWorkUnitItem($client, $jobId1);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(0, $status['value']);

        $jobId2 = $this->uploadFile($client, 'su1.csv', self::URL);

        $this->runJob($client, $jobId2, self::URL);
        $response = $this->fetchApiWorkUnitItem($client, $jobId2);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(0, $status['value']);

        $this->consumeQueue('async', 2);

        $response = $this->fetchApiWorkUnitItem($client, $jobId1);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(2, $status['value']);

        $response = $this->fetchApiWorkUnitItem($client, $jobId2);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(2, $status['value']);
    }


    public function testAdminSuccess()
    {
        $client = $this->createAuthenticatedClient(self::USER_ADMIN, self::USER_ADMIN_PW);

        $jobId = $this->uploadFile($client, 'su.csv', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();
        $response = $this->fetchApiWorkUnitCollection($client);
        $this->assertEquals(2, $response->toArray()['totalItems']);
    }

    public function testHeadersValidationError()
    {
        $client = $this->createAuthenticatedClient();
        $jobId = $this->uploadFile($client, 'su__headers_err.csv', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testDataValidationError()
    {
        $client = $this->createAuthenticatedClient();
        $jobId = $this->uploadFile($client, 'su__err1.csv', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
        $response = $client->request(
            'GET',
            "api/import_files/$jobId",
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertResponseIsSuccessful();
    }

    public function testWrongFileMimeTypeError()
    {
        $client = $this->createAuthenticatedClient();
        $jobId = $this->uploadFile($client, 'test.pdf', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testFakeCsvFileError()
    {
        $client = $this->createAuthenticatedClient();
        $jobId = $this->uploadFile($client, 'fake.csv', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testAuthFileError()
    {
        $client = $this->createAuthenticatedClient();
        $jobId = $this->uploadFile($client, 'su__auth_err.csv', self::URL);

        $this->runJob($client, $jobId, self::URL);

        $this->consumeQueue();

        $response = $this->fetchApiWorkUnitItem($client, $jobId);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }

    public function testOneErrorOneSuccess()
    {
        $client = $this->createAuthenticatedClient();
        $jobId1 = $this->uploadFile($client, 'su__headers_err.csv', self::URL);
        $jobId2 = $this->uploadFile($client, 'su.csv', self::URL);

        $this->runJob($client, $jobId1, self::URL);
        $this->runJob($client, $jobId2, self::URL);

        $this->consumeQueue('async', 2);

        $response = $this->fetchApiWorkUnitItem($client, $jobId1);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(8, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));

        $response = $this->fetchApiWorkUnitItem($client, $jobId2);
        $status = $this->getJsonResponseValue($response, 'status');
        $this->assertEquals(2, $status['value']);
        $this->assertIsArray($this->getJsonResponseValue($response, 'errors'));
    }
}
