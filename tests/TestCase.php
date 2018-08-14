<?php

namespace Neospheres\Keycloak\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TestCase extends BaseTestCase
{
    /**
     * @param string|null $streamData
     * @return MockObject
     */
    protected function createResponseMock($streamData = null)
    {
        $streamMock = $this->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $streamMock->expects($this->any())->method('getContents')
            ->willReturn($streamData);
        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $responseMock->expects($this->any())->method('getBody')
            ->willReturn($streamMock);

        return $responseMock;
    }

    /**
     * @return MockObject
     */
    protected function createRequestMock()
    {
        return $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}