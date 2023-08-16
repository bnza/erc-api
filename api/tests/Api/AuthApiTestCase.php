<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthApiTestCase extends ApiTestCase
{
    use RefreshDatabaseTrait;
    public const LOGIN_PATH = '/login';

    private string $jwtToken;

    public const USER_BASE = 'user_base@example.com';
    public const USER_BASE_PW = '0000';
    public const USER_EDITOR = 'user_editor@example.com';
    public const USER_EDITOR_PW = '0001';
    public const USER_ADMIN = 'user_admin@example.com';
    public const USER_ADMIN_PW = '0002';

    protected static array $userBaseCredentials = [
      'username' => self::USER_BASE,
      'password' => self::USER_BASE_PW,
    ];

    protected static array $userEditorCredentials = [
        'username' => self::USER_EDITOR,
        'password' => self::USER_EDITOR_PW,
    ];

    protected static array $userAdminCredentials = [
        'username' => self::USER_ADMIN,
        'password' => self::USER_ADMIN_PW,
    ];

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     */
    private function getToken(
        ?string $username = self::USER_BASE,
        ?string $password = self::USER_BASE_PW
    ) {
        if (isset($this->jwtToken)) {
            return $this->jwtToken;
        }

        $response = static::createClient()->request('POST', self::LOGIN_PATH, [
            'json' => [
                'email' => $username,
                'password' => $password,
            ],
        ]);

        if ($response->getStatusCode() < 300) {
            $content = $response->toArray();
            $this->jwtToken = $content['token'];
        }

        return $this->jwtToken;
    }

    protected function getAuthorizationHeaderString(): ?string
    {
        return isset($this->jwtToken) ? "Bearer $this->jwtToken" : null;
    }

    protected function createAuthenticatedClient(?string $username = self::USER_BASE, ?string $password = self::USER_BASE_PW): Client
    {
        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$this->getToken($username, $password)]]);
    }
}
