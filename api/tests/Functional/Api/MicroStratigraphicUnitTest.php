<?php

namespace App\Tests\Functional\Api;

class MicroStratigraphicUnitTest extends AuthApiTestCase
{
    public function testCreateSuccess(): void
    {
        $client = $this->createAuthenticatedClient(self::USER_GEO, self::USER_GEO_PW);

        $response = $client->request('GET', 'api/sites?code=AN');
        $siteId = $this->getJsonResponseValue($response, 'member')[0]['id'];
        $response = $client->request(
            'GET',
            "api/samples?order[id]=asc&page=1&itemsPerPage=10&stratigraphicUnit.site[0]=$siteId&number=3"
        );
        $sampleId = $this->getJsonResponseValue($response, 'member')[0]['@id'];
        $response = $client->request(
            'GET',
            "api/stratigraphic_units?order[id]=asc&page=1&itemsPerPage=10&site[0]=$siteId&number=3&year=2024&number=1004"
        );
        $suId = $this->getJsonResponseValue($response, 'member')[0]['@id'];
        $body = sprintf(
            '{"sample":"%s","stratigraphicUnit":"%s","number":2,"depositType":"Buried horizon C","keyAttributes":"key attribute","inclusionsGeology":50,"inclusionsBuildingMaterials":50,"inclusionsDomesticRefuse":0,"inclusionsOrganicRefuse":0,"colourPpl":"yellow","colourXpl":"brown","colourOil":"cyan"}',
            $sampleId,
            $suId
        );
        $response = $client->request(
            'POST',
            '/api/micro_stratigraphic_units',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
                'body' => $body,
            ]
        );

        $this->assertResponseIsSuccessful();
    }
}
