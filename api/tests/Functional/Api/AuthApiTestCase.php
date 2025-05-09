<?php

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthApiTestCase extends ApiTestCase
{
    public const API_PREFIX = '/api';
    public const LOGIN_PATH = '/login';

    private array $jwtToken = [];

    public const USER_BASE = 'user_base@example.com';
    public const USER_BASE_PW = '0000';
    public const USER_EDITOR = 'user_editor@example.com';
    public const USER_EDITOR_PW = '0001';
    public const USER_ADMIN = 'user_admin@example.com';
    public const USER_ADMIN_PW = '0002';

    public const USER_GEO = 'user_geo@example.com';
    public const USER_GEO_PW = '0003';

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
        ?string $password = self::USER_BASE_PW,
    ) {
        if (array_key_exists($username, $this->jwtToken)) {
            return $this->jwtToken[$username];
        }

        $response = static::createClient()->request('POST', self::API_PREFIX.self::LOGIN_PATH, [
            'json' => [
                'email' => $username,
                'password' => $password,
            ],
        ]);

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Unexpected HTTP status code "'.$response->getStatusCode().'"');
        }

        $content = $response->toArray();
        $this->jwtToken[$username] = $content['token'];

        return $this->jwtToken[$username];
    }

    protected function getAuthorizationHeaderString(?string $username = self::USER_BASE): ?string
    {
        return array_key_exists($username, $this->jwtToken) ? sprintf('Bearer %s', $this->jwtToken[$username]) : null;
    }

    protected function createAuthenticatedClient(
        ?string $username = self::USER_BASE,
        ?string $password = self::USER_BASE_PW,
        ?string $contentType = 'application/ld+json',
    ): Client {
        return static::createClient([],
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->getToken($username, $password),
                    'Content-Type' => $contentType,
                ],
            ]);
    }

    protected function getJsonResponseValue(
        ResponseInterface $response,
        ?string $key,
        ?string $format = 'jsonld',
    ): mixed {
        if (!in_array($format, ['jsonld', 'json'])) {
            throw new \InvalidArgumentException("Unsupported format: \"$format\"");
        }
        $this->assertResponseFormatSame($format);
        $values = $response->toArray();
        if ($key) {
            if (!array_key_exists($key, $values)) {
                throw new \InvalidArgumentException("Key \"$key\" does not exist");
            }

            return $values[$key];
        }

        return $values;
    }

    protected function getJsonResponseId(ResponseInterface $response, ?string $format = 'jsonld'): int|Uuid
    {
        $id = $this->getJsonResponseValue($response, 'id', $format);

        return match (true) {
            is_numeric($id) => (int) $id,
            Uuid::isValid($id) => Uuid::fromString($id),
            default => throw new \RuntimeException("Unsupported response type: \"$id\""),
        };
    }
}
