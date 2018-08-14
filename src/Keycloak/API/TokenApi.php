<?php

namespace Neospheres\Keycloak\API;

use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Exceptions\TokenException;
use Neospheres\Keycloak\Models\TokenInfo;
use Neospheres\Keycloak\Http\ApiClient;
use GuzzleHttp\Client;

class TokenApi
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

    /**
     * @param string $token
     * @return TokenInfo
     * @throws HttpException
     * @throws TokenException
     */
    public function getUserInfo($token)
    {
        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $this->getUserInfoUrl(),
                $token
            );

            return new TokenInfo(json_decode($response->getBody()->getContents(), true));
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TokenException::failedToGetToken($e);
        }
    }

    /**
     * @return string
     */
    private function getUserInfoUrl()
    {
        return sprintf(
            'realms/%s/protocol/openid-connect/userinfo',
            $this->realm
        );
    }

}