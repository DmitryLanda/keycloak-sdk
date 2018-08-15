<?php

namespace Neospheres\Keycloak\API;

use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Exceptions\TokenException;
use Neospheres\Keycloak\Models\TokenInfo;
use Neospheres\Keycloak\Http\ApiClient;
use GuzzleHttp\Client;

class AuthApi
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $realm;

    /**
     * TokenApi constructor.
     * @param ApiClient $client
     * @param string $realm
     */
    public function __construct(ApiClient $client, $realm)
    {
        $this->client = $client;
        $this->realm = $realm;
    }

    public function authorizeWithPassword($clientId, $login, $password)
    {
        $response = $this->client->makeFormRequest(
            'POST',
            $this->getAuthUrl(),
            [
                'grant_type' => 'password',
                'client_id' => $clientId,
                'username' => $login,
                'password' => $password
            ]
        );

        return $response['access_token'] ?? null;
    }

    private function getAuthUrl()
    {
        return sprintf('realms/%s/protocol/openid-connect/token', $this->realm);
    }
}