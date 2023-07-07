<?php

namespace App\Tests\Api;

/**
 * @group WIP
 */
class AuthenticationTest extends AuthApiTestCase
{
    public function testAuth()
    {
        $client = self::createClient();
        $response = $this->authenticate($client);
        $this->assertResponseIsSuccessful();
    }
}
