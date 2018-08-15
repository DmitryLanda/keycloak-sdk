<?php

namespace Neospheres\Keycloak\Test\API;

use Neospheres\Keycloak\API\AuthApi;
use Neospheres\Keycloak\Http\ApiClient;
use Neospheres\Keycloak\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AuthApiTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $apiClientMock;

    /**
     * @var AuthApi
     */
    private $authApi;

    protected function setUp()
    {
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authApi = new AuthApi($this->apiClientMock, 'realm');
    }

    /**
     * @test
     */
    public function authorizeWithPassword()
    {
        $responseMock = [

            'access_token' => "token",
            'expires_in' => 300,
            'refresh_expires_in' => 36000,
            'refresh_token' => "refresh",
            'token_type' => "bearer",
            'not-before-policy' => 1529734031,
            'session_state' => 'state'
        ];

        $this->apiClientMock->expects($this->once())->method('makeFormRequest')
            ->with(
                'POST',
                'realms/realm/protocol/openid-connect/token',
                [
                    'grant_type' => 'password',
                    'client_id' => 'foo',
                    'username' => 'bar',
                    'password' => 'buz'
                ]
            )

            ->willReturn($responseMock);

        $this->authApi->authorizeWithPassword('foo', 'bar', 'buz');
    }
}
