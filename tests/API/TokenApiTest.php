<?php
namespace Neospheres\Keycloak\Test\API;

use Neospheres\Keycloak\API\TokenApi;
use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Http\ApiClient;
use PHPUnit\Framework\MockObject\MockObject;
use Neospheres\Keycloak\Test\TestCase;

class TokenApiTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $apiClientMock;

    /**
     * @var TokenApi
     */
    private $tokenApi;

    protected function setUp()
    {
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->tokenApi = new TokenApi($this->apiClientMock, 'realm');
    }

    /**
     * @test
     */
    public function getUserInfo()
    {
        $responseMock = $this->createResponseMock('{"sub":123,"username":"foo","email":"bar@email.com"}');
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with('GET', 'realms/realm/protocol/openid-connect/userinfo', 'token')
            ->willReturn($responseMock)
        ;

        $tokenInfo = $this->tokenApi->getUserInfo('token');

        $this->assertEquals(123, $tokenInfo->getId());
        $this->assertEquals('foo', $tokenInfo->getUsername());
        $this->assertEquals('bar@email.com', $tokenInfo->getEmail());
    }

    /**
     * @test
     * @expectedException \Neospheres\Keycloak\Exceptions\HttpException
     */
    public function requestWithHttpError()
    {
        $this->apiClientMock->method('makeJsonRequest')
            ->willThrowException(new HttpException())
        ;

        $this->tokenApi->getUserInfo('token');
    }

    /**
     * @test
     * @expectedException \Neospheres\Keycloak\Exceptions\TokenException
     */
    public function requestWithCommonError()
    {
        $this->apiClientMock->method('makeJsonRequest')
            ->willThrowException(new \Exception())
        ;

        $this->tokenApi->getUserInfo('token');
    }
}