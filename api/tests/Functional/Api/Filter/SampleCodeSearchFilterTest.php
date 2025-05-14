<?php

namespace App\Tests\Functional\Api\Filter;

use App\Tests\Functional\Api\AuthApiTestCase;

class SampleCodeSearchFilterTest extends AuthApiTestCase
{
    /**
     * Test that the search filter works when providing a site code (string pattern).
     */
    public function testSearchBySiteCode(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a site code to search for
        $response = $client->request('GET', 'api/sites');
        $this->assertResponseIsSuccessful();

        $sites = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($sites, 'Sites collection should not be empty');

        $siteCode = $sites[0]['code'];

        // Search for samples with this site code
        $response = $client->request('GET', "api/autocomplete/samples?search=$siteCode");
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should all have stratigraphic units from the site with that code
        if (!empty($samples)) {
            foreach ($samples as $sample) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $sample['stratigraphicUnit']['site']['code'],
                    'All results should be from sites with matching code');
            }
        }
    }

    /**
     * Test that the search filter works when providing a numeric value (int pattern).
     */
    public function testSearchByNumeric(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a sample to use its stratigraphic unit year or number
        $response = $client->request('GET', 'api/samples');
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($samples, 'Samples collection should not be empty');

        // Try with stratigraphic unit year
        $numeric = $samples[0]['stratigraphicUnit']['year'];

        // Search for samples with this numeric value (should match stratigraphic unit year)
        $response = $client->request('GET', "api/autocomplete/samples?search=$numeric");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, at least one should contain the numeric value in either stratigraphic unit year or sample number
        if (!empty($searchResults)) {
            $foundMatch = false;
            foreach ($searchResults as $sample) {
                if ((string) $sample['stratigraphicUnit']['year'] === (string) $numeric
                    || (string) $sample['number'] === (string) $numeric) {
                    $foundMatch = true;
                    break;
                }
            }
            $this->assertTrue($foundMatch, 'At least one result should match the numeric search in either stratigraphic unit year or sample number');
        }
    }

    /**
     * Test that the search filter works with site code and numeric pattern.
     */
    public function testSearchBySiteCodeAndNumeric(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a sample to use its stratigraphic unit site code and year
        $response = $client->request('GET', 'api/samples');
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($samples, 'Samples collection should not be empty');

        $siteCode = $samples[0]['stratigraphicUnit']['site']['code'];
        $numeric = $samples[0]['stratigraphicUnit']['year']; // Use stratigraphic unit year

        // Search with both site code and numeric value
        $response = $client->request('GET', "api/autocomplete/samples?search=$siteCode.$numeric");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should match both criteria
        if (!empty($searchResults)) {
            foreach ($searchResults as $sample) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $sample['stratigraphicUnit']['site']['code'],
                    'All results should be from sites with matching code');

                $matchesNumeric = false;
                if ((string) $sample['stratigraphicUnit']['year'] === (string) $numeric
                    || (string) $sample['number'] === (string) $numeric) {
                    $matchesNumeric = true;
                }
                $this->assertTrue($matchesNumeric, 'All results should match the numeric part of the search in either stratigraphic unit year or sample number');
            }
        }
    }

    /**
     * Test that the search filter works with site code, stratigraphic unit year, and sample number pattern.
     */
    public function testSearchBySiteCodeYearNumber(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a sample to use its stratigraphic unit site code, year, and number
        $response = $client->request('GET', 'api/samples');
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($samples, 'Samples collection should not be empty');

        $siteCode = $samples[0]['stratigraphicUnit']['site']['code'];
        $year = $samples[0]['stratigraphicUnit']['year'];
        $number = $samples[0]['number'];

        // Search with site code, stratigraphic unit year, and sample number
        $response = $client->request('GET', "api/autocomplete/samples?search=$siteCode.$year.$number");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should match all three criteria
        if (!empty($searchResults)) {
            foreach ($searchResults as $sample) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $sample['stratigraphicUnit']['site']['code'],
                    'All results should be from sites with matching code');
                $this->assertStringContainsString((string) $year, (string) $sample['stratigraphicUnit']['year'],
                    'All results should match the stratigraphic unit year part');
                $this->assertStringContainsString((string) $number, (string) $sample['number'],
                    'All results should match the sample number part');
            }
        }
    }

    /**
     * Test that the search filter works with two numeric values (year and number).
     */
    public function testSearchByMultiNumeric(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a sample to use its stratigraphic unit year and number
        $response = $client->request('GET', 'api/samples');
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($samples, 'Samples collection should not be empty');

        $year = $samples[0]['stratigraphicUnit']['year'];
        $number = $samples[0]['number'];

        // Search with both year and number
        $response = $client->request('GET', "api/autocomplete/samples?search=$year.$number");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should match both criteria
        if (!empty($searchResults)) {
            foreach ($searchResults as $sample) {
                $this->assertStringContainsString((string) $year, (string) $sample['stratigraphicUnit']['year'],
                    'All results should match the stratigraphic unit year part');
                $this->assertStringContainsString((string) $number, (string) $sample['number'],
                    'All results should match the sample number part');
            }
        }
    }

    /**
     * Test that extra values in the search string beyond 3 are discarded.
     */
    public function testExtraValuesAreDiscarded(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a sample to use its data
        $response = $client->request('GET', 'api/samples');
        $this->assertResponseIsSuccessful();

        $samples = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($samples, 'Samples collection should not be empty');

        $siteCode = $samples[0]['stratigraphicUnit']['site']['code'];
        $year = $samples[0]['stratigraphicUnit']['year'];
        $number = $samples[0]['number'];

        // Search with 3 correct values plus an extra value that should be discarded
        $response1 = $client->request('GET', "api/samples?search=$siteCode.$year.$number");
        $response2 = $client->request('GET', "api/samples?search=$siteCode.$year.$number.extravalue");

        $this->assertResponseIsSuccessful();

        $results1 = $this->getJsonResponseValue($response1, 'member');
        $results2 = $this->getJsonResponseValue($response2, 'member');

        // Both searches should return the same results since the extra value should be discarded
        $this->assertEquals(
            count($results1),
            count($results2),
            'Search with extra values should return the same number of results as search with just 3 values'
        );

        if (!empty($results1) && !empty($results2)) {
            // Compare first item from each result to verify they're the same
            $this->assertEquals(
                $results1[0]['id'],
                $results2[0]['id'],
                'Search results should be identical regardless of extra values'
            );
        }
    }
}
