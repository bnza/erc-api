<?php

namespace App\Tests\Functional\Api\Resource\Validator;

use App\Tests\Functional\Api\AuthApiTestCase;

class UniqueStratigraphicUnitTest extends AuthApiTestCase
{
    /**
     * Test that checks if the uniqueness validation works correctly when a stratigraphic unit doesn't exist.
     */
    public function testUniqueStratigraphicUnitSuccess(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        // First, get a site
        $response = $client->request('GET', 'api/sites?code=AN');
        $this->assertResponseIsSuccessful();
        $site = $this->getJsonResponseValue($response, 'member')[0];
        $siteId = $site['id'];

        // Query the unique validator endpoint with parameters that shouldn't exist
        // Using a non-existent year and number
        $response = $client->request(
            'GET',
            sprintf('/api/validator/unique/stratigraphic_units/%d/%d/%d', $siteId, 9999, 9999),
            ['headers' => ['Accept' => 'application/json']]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        // If unique, the response should have unique=true
        $result = (int) $response->getContent();
        $this->assertEquals(1, $result, 'The stratigraphic unit should be unique');
    }

    /**
     * Test that checks if the uniqueness validation detects existing stratigraphic units.
     */
    public function testUniqueStratigraphicUnitFail(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        // Get a site
        $response = $client->request('GET', 'api/sites?code=AN');
        $this->assertResponseIsSuccessful();
        $site = $this->getJsonResponseValue($response, 'member')[0];
        $siteId = $site['id'];

        // First, find an existing stratigraphic unit
        $response = $client->request(
            'GET',
            sprintf('api/stratigraphic_units?site[0]=%d&itemsPerPage=1', $siteId)
        );
        $this->assertResponseIsSuccessful();

        $existingSU = $this->getJsonResponseValue($response, 'member')[0];

        // Get the year and number directly from the response
        $year = $existingSU['year'];
        $number = $existingSU['number'];

        // Query the unique validator endpoint with parameters that should already exist
        $response = $client->request(
            'GET',
            sprintf('/api/validator/unique/stratigraphic_units/%d/%s/%s', $siteId, $year, $number),
            ['headers' => ['Accept' => 'application/json']]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        // If not unique, the response should have unique=false

        $result = (int) $response->getContent();
        $this->assertEquals(0, $result, 'The stratigraphic unit should not be unique');
    }

    /**
     * Test that validates input requirement validation works.
     */
    public function testInvalidRequirements(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        // Test with non-numeric site
        $response = $client->request(
            'GET',
            '/api/validator/unique/stratigraphic_units/invalid/2024/1',
        );

        $this->assertResponseStatusCodeSame(404);

        // Test with non-numeric year
        $response = $client->request(
            'GET',
            '/api/validator/unique/stratigraphic_units/1/invalid/1',
        );

        $this->assertResponseStatusCodeSame(404);

        // Test with non-numeric number
        $response = $client->request(
            'GET',
            '/api/validator/unique/stratigraphic_units/1/2024/invalid',
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
