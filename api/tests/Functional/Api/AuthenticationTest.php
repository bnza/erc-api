<?php

namespace App\Tests\Functional\Api;


use PHPUnit\Framework\Attributes\DataProvider;

class AuthenticationTest extends AuthApiTestCase
{
    public static function authCredentialsProvider(): array
    {
        return [
            [self::USER_BASE, self::USER_BASE_PW],
            [self::USER_EDITOR, self::USER_EDITOR_PW],
            [self::USER_ADMIN, self::USER_ADMIN_PW],
        ];
    }

    #[DataProvider('authCredentialsProvider')]
    public function testAuth(string $username, string $password)
    {
        $this->assertEmpty($this->getAuthorizationHeaderString());
        $this->createAuthenticatedClient($username, $password);
        $this->assertIsString($this->getAuthorizationHeaderString($username));
    }

    /**
     * @group WIP
     */
    public function testMe()
    {
        $this->assertEmpty($this->getAuthorizationHeaderString());
        $client = $this->createAuthenticatedClient();
        $client->request('GET', self::API_PREFIX.'/users/me', ['headers' => ['Accept' => 'application/json']]);
        $this->assertResponseIsSuccessful();
    }
}
