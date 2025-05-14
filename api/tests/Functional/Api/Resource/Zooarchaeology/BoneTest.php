<?php

namespace App\Tests\Functional\Api\Resource\Zooarchaeology;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class BoneTest extends ApiTestCase
{
    /**
     * Test that the collection endpoint returns a valid response.
     */
    public function testGetBoneCollection(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', 'api/zooarchaeology/bones');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Bone',
            '@id' => '/api/zooarchaeology/bones',
            '@type' => 'Collection',
        ]);
    }

    /**
     * Test that an individual item can be retrieved.
     */
    public function testGetBoneItem(): void
    {
        $client = static::createClient();

        // First, get the collection to find an ID to use
        $response = $client->request('GET', 'api/zooarchaeology/bones?page=1&itemsPerPage=1');
        $this->assertResponseIsSuccessful();

        // Extract the ID of the first bone in the collection
        $data = $response->toArray();

        // Check if there's at least one item in the collection
        if (empty($data['member'])) {
            $this->markTestSkipped('No bone records available to test with');
        }

        $boneId = $data['member'][0]['id'];

        // Now request the individual bone item
        $response = $client->request('GET', "api/zooarchaeology/bones/{$boneId}");

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Bone',
            '@type' => 'Bone',
            'id' => $boneId,
        ]);
    }
}
