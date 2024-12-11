<?php

namespace App\Tests\Api;


class AuthenticationTest extends AuthApiTestCase
{
    public function authCredentialsProvider(): array
    {
        return [
            [self::USER_BASE, self::USER_BASE_PW],
            [self::USER_EDITOR, self::USER_EDITOR_PW],
            [self::USER_ADMIN, self::USER_ADMIN_PW],
            [self::USER_GEO, self::USER_GEO_PW],
        ];
    }

    /**
     * @dataProvider authCredentialsProvider
     */
    public function testAuth(string $username, string $password)
    {
        $this->assertEmpty($this->getAuthorizationHeaderString());
        $this->createAuthenticatedClient($username, $password);
        $this->assertIsString($this->getAuthorizationHeaderString());
    }

    /**
     * @group WIP
     */
    public function testMe()
    {
        $this->assertEmpty($this->getAuthorizationHeaderString());
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/users/me');
        $this->assertResponseIsSuccessful();
    }
}
