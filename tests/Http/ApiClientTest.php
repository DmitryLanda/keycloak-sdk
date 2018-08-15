<?php

namespace Neospheres\Keycloak\Test\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Neospheres\Keycloak\Http\ApiClient;
use PHPUnit\Framework\MockObject\MockObject;
use Neospheres\Keycloak\Test\TestCase;

class ApiClientTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $transport;

    /**
     * @var ApiClient
     */
    private $apiClient;

    protected function setUp()
    {
        $this->transport = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->apiClient = new ApiClient(
            'http://sso',
            $this->transport
        );
    }

    /**
     * @test
     */
    public function makeSuccessfulJsonRequest()
    {
        $responseMock = $this->createResponseMock();

        $this->transport->expects($this->once())->method('request')
            ->with(
                'GET',
                'any-url',
                [
                    'headers' => ['Authorization' => 'Bearer token']
                ]
            )
            ->willReturn($responseMock);

        $this->apiClient->makeJsonRequest('GET', 'any-url', 'token');
    }

    /**
     * @test
     * @expectedException \Neospheres\Keycloak\Exceptions\HttpException
     */
    public function makeJsonRequestWithError()
    {
        $this->transport->expects($this->once())->method('request')
            ->willThrowException(new BadResponseException(
                'error',
                $this->createRequestMock(),
                $this->createResponseMock()
            ));

        $this->apiClient->makeJsonRequest('GET', 'bad-url', 'token');
    }

    /**
     * @test
     */
    public function makeSuccessfulFormRequest()
    {
        $responseMock = $this->createResponseMock();

        $this->transport->expects($this->once())->method('request')
            ->with(
                'GET',
                'any-url',
                [
                    'form_params' => [
                        'foo' => 1,
                        'bar' => 2
                    ]
                ]
            )

            ->willReturn($responseMock);

        $this->apiClient->makeFormRequest('GET', 'any-url', ['foo' => 1, 'bar' => 2]);
    }

    /**
     * @test
     * @expectedException \Neospheres\Keycloak\Exceptions\HttpException
     */
    public function makeFormRequestWithError()
    {
        $this->transport->expects($this->once())->method('request')
            ->willThrowException(new BadResponseException(
                'error',
                $this->createRequestMock(),
                $this->createResponseMock()
            ));

        $this->apiClient->makeJsonRequest('GET', 'bad-url', 'token');
    }
}
