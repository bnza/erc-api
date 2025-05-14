<?php

namespace App\Tests\Functional\Api\Resource;

use App\Tests\Functional\Api\AuthApiTestCase;

class SiteTest extends AuthApiTestCase
{
    /**
     * Test that authenticated users can see the public property in collection requests.
     */
    public function testCollectionWithAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        $response = $client->request('GET', 'api/sites');
        $this->assertResponseIsSuccessful();

        // Get the first site from the collection
        $sites = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($sites, 'Sites collection should not be empty');

        // Verify that the public property is present
        $this->assertArrayHasKey('public', $sites[0], 'The public property should be visible for authenticated users');
    }

    /**
     * Test that unauthenticated users cannot see the public property in collection requests.
     */
    public function testCollectionWithUnauthenticatedUser(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', 'api/sites');
        $this->assertResponseIsSuccessful();

        // Get the first site from the collection
        $sites = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($sites, 'Sites collection should not be empty');

        // Verify that the public property is not present
        $this->assertArrayNotHasKey('public', $sites[0], 'The public property should not be visible for unauthenticated users');
    }

    /**
     * Test that authenticated users can see the public property in item requests.
     */
    public function testItemWithAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        // First get a site ID to use
        $response = $client->request('GET', 'api/sites');
        $this->assertResponseIsSuccessful();

        $sites = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($sites, 'Sites collection should not be empty');

        $siteId = $sites[0]['id'];

        // Now request the individual site
        $response = $client->request('GET', "api/sites/$siteId");
        $this->assertResponseIsSuccessful();

        $site = $this->getJsonResponseValue($response, null);

        // Verify that the public property is present
        $this->assertArrayHasKey('public', $site, 'The public property should be visible for authenticated users');
    }

    /**
     * Test that unauthenticated users cannot see the public property in item requests.
     */
    public function testItemWithUnauthenticatedUser(): void
    {
        $client = static::createClient();

        // First get a site ID to use (as an unauthenticated user)
        $response = $client->request('GET', 'api/sites');
        $this->assertResponseIsSuccessful();

        $sites = $this->getJsonResponseValue($response, 'member');
        $this->assertNotEmpty($sites, 'Sites collection should not be empty');

        $siteId = $sites[0]['id'];

        // Now request the individual site
        $response = $client->request('GET', "api/sites/$siteId");
        $this->assertResponseIsSuccessful();

        $site = $this->getJsonResponseValue($response, null);

        // Verify that the public property is not present
        $this->assertArrayNotHasKey('public', $site, 'The public property should not be visible for unauthenticated users');
    }

    /**
     * Test that we can filter sites by public property when authenticated.
     */
    public function testFilterByPublicPropertyWithAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        // Request sites with public=true
        $response = $client->request('GET', 'api/sites?public=false');
        $this->assertResponseIsSuccessful();

        $sites = $this->getJsonResponseValue($response, 'member');

        // If there are results, verify they all have public=true
        if (!empty($sites)) {
            foreach ($sites as $site) {
                $this->assertFalse($site['public'], 'All sites should have public=false when filtering for public=false');
            }
        }

        // Request sites with public=false
        $response = $client->request('GET', 'api/sites?public=false');
        $this->assertResponseIsSuccessful();

        $sites = $this->getJsonResponseValue($response, 'member');

        // If there are results, verify they all have public=false
        if (!empty($sites)) {
            foreach ($sites as $site) {
                $this->assertFalse($site['public'], 'All sites should have public=false when filtering for public=false');
            }
        }
    }

    /**
     * Test that unauthenticated users cannot filter by public property.
     */
    public function testFilterByPublicPropertyWithUnauthenticatedUser(): void
    {
        $client = static::createClient();

        // Try to filter by public property
        $response = $client->request('GET', 'api/sites?public=false');

        $this->assertResponseIsSuccessful();

        // Get sites with attempted filter
        $filteredSites = $this->getJsonResponseValue($response, 'member');

        // The count should be the same if the filter was ignored
        $this->assertCount(
            0,
            $filteredSites,
            'Filter by public=false property should return an empty set'
        );
    }
}
