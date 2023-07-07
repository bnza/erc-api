<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthApiTestCase extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private string $jwtToken;

    public const USER_BASE = 'user_base@example.com';
    public const USER_BASE_PW = '0000';
    public const USER_EDITOR = 'user_editor@example.com';
    public const USER_EDITOR_PW = '0001';
    public const USER_ADMIN = 'user_admin@example.com';
    public const USER_ADMIN_PW = '0002';

    protected static array $userBaseCredentials = [
      'username' =>  self::USER_BASE,
      'password' => self::USER_BASE_PW
    ];

    protected static array $userEditorCredentials = [
        'username' =>  self::USER_EDITOR,
        'password' => self::USER_EDITOR_PW
    ];

    protected static array $userAdminCredentials = [
        'username' =>  self::USER_ADMIN,
        'password' => self::USER_ADMIN_PW
    ];

    protected function authenticate(Client $client, ?string $username = self::USER_BASE, ?string $password = self::USER_BASE_PW, $throw = true): ResponseInterface
    {
        $response = $client->request('POST', '/auth', [
            'json' => [
                'email' => $username,
                'password' => $password,
            ],
        ]);

        if ($response->getStatusCode() < 300) {
            $content = json_decode($response->getContent($throw), true);
            $this->jwtToken = $content['token'];
        }

        return $response;
    }



}
