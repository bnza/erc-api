<?php

namespace App\Tests\Functional\Api\Resource;

use App\Tests\Functional\Api\AuthApiTestCase;

class StratigraphicUnitTest extends AuthApiTestCase
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

        // Search for stratigraphic units with this site code
        $response = $client->request('GET', "api/autocomplete/stratigraphic_units?search=$siteCode");
        $this->assertResponseIsSuccessful();

        $units = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should all be from the site with that code
        if (!empty($units)) {
            foreach ($units as $unit) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $unit['site']['code'],
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

        // Get a stratigraphic unit to use its year or number
        $response = $client->request('GET', 'api/stratigraphic_units');
        $this->assertResponseIsSuccessful();

        $units = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($units, 'Stratigraphic units collection should not be empty');

        $numeric = $units[0]['year']; // Using year as the numeric value to search

        // Search for stratigraphic units with this numeric value
        $response = $client->request('GET', "api/autocomplete/stratigraphic_units?search=$numeric");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should contain the numeric value in either year or number
        if (!empty($searchResults)) {
            $foundMatch = false;
            foreach ($searchResults as $unit) {
                if ((string) $unit['year'] === (string) $numeric || (string) $unit['number'] === (string) $numeric) {
                    $foundMatch = true;
                    break;
                }
            }
            $this->assertTrue($foundMatch, 'At least one result should match the numeric search');
        }
    }

    /**
     * Test that the search filter works with site code and numeric pattern.
     */
    public function testSearchBySiteCodeAndNumeric(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a stratigraphic unit to use its site code and year
        $response = $client->request('GET', 'api/stratigraphic_units');
        $this->assertResponseIsSuccessful();

        $units = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($units, 'Stratigraphic units collection should not be empty');

        $siteCode = $units[0]['site']['code'];
        $numeric = $units[0]['year'];

        // Search with both site code and numeric value
        $response = $client->request('GET', "api/autocomplete/stratigraphic_units?search=$siteCode.$numeric");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should match both criteria
        if (!empty($searchResults)) {
            foreach ($searchResults as $unit) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $unit['site']['code'],
                    'All results should be from sites with matching code');

                $matchesNumeric = false;
                if ((string) $unit['year'] === (string) $numeric || (string) $unit['number'] === (string) $numeric) {
                    $matchesNumeric = true;
                }
                $this->assertTrue($matchesNumeric, 'All results should match the numeric part of the search');
            }
        }
    }

    /**
     * Test that the search filter works with site code, year, and number pattern.
     */
    public function testSearchBySiteCodeYearNumber(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a stratigraphic unit to use its site code, year, and number
        $response = $client->request('GET', 'api/stratigraphic_units');
        $this->assertResponseIsSuccessful();

        $units = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($units, 'Stratigraphic units collection should not be empty');

        $siteCode = $units[0]['site']['code'];
        $year = $units[0]['year'];
        $number = $units[0]['number'];

        // Search with site code, year, and number
        $response = $client->request('GET', "api/autocomplete/stratigraphic_units?search=$siteCode.$year.$number");
        $this->assertResponseIsSuccessful();

        $searchResults = $this->getJsonResponseValue($response, 'member');

        // If there are any results, they should match all three criteria
        if (!empty($searchResults)) {
            foreach ($searchResults as $unit) {
                $this->assertStringContainsStringIgnoringCase($siteCode, $unit['site']['code'],
                    'All results should be from sites with matching code');
                $this->assertStringContainsString((string) $year, (string) $unit['year'],
                    'All results should match the year part');
                $this->assertStringContainsString((string) $number, (string) $unit['number'],
                    'All results should match the number part');
            }
        }
    }

    /**
     * Test that extra values in the search string beyond 3 are discarded.
     */
    public function testExtraValuesAreDiscarded(): void
    {
        $client = $this->createUnauthenticatedClient();

        // Get a stratigraphic unit to use its data
        $response = $client->request('GET', 'api/stratigraphic_units');
        $this->assertResponseIsSuccessful();

        $units = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($units, 'Stratigraphic units collection should not be empty');

        $siteCode = $units[0]['site']['code'];
        $year = $units[0]['year'];
        $number = $units[0]['number'];

        // Search with 3 correct values plus an extra value that should be discarded
        $response1 = $client->request('GET', "api/stratigraphic_units?search=$siteCode.$year.$number");
        $response2 = $client->request('GET', "api/stratigraphic_units?search=$siteCode.$year.$number.extravalue");

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
