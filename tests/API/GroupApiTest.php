<?php
namespace Neospheres\Keycloak\Test\API;

use Neospheres\Keycloak\API\GroupApi;
use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Http\ApiClient;
use Neospheres\Keycloak\Models\GroupRequest;
use PHPUnit\Framework\MockObject\MockObject;
use Neospheres\Keycloak\Test\TestCase;

class GroupApiTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $apiClientMock;

    /**
     * @var GroupApi
     */
    private $groupApi;

    protected function setUp()
    {
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->apiClientMock->expects($this->any())->method('authorizeAsAdmin')
            ->willReturn('token')
        ;
        $this->groupApi = new GroupApi($this->apiClientMock, 'realm');
    }

    /**
     * @test
     * @dataProvider searchDataProvider
     * @param integer $userId
     * @param string $expectedUrl
     */
    public function search($userId, $expectedUrl)
    {
        $result = '[
            {"id":123,"name":"foo1"},
            {"id":456,"name":"foo2"}
        ]';
        $responseMock = $this->createResponseMock($result);
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with('GET', $expectedUrl, 'token')
            ->willReturn($responseMock)
        ;

        $groups = $this->groupApi->search($userId);

        $this->assertCount(2, $groups);

        $this->assertEquals(123, $groups[0]->getId());
        $this->assertEquals('foo1', $groups[0]->getName());

        $this->assertEquals(456, $groups[1]->getId());
        $this->assertEquals('foo2', $groups[1]->getName());
    }

    /**
     * @return array
     */
    public function searchDataProvider()
    {
        return [
            [123, 'admin/realms/realm/users/123/groups'],
            [null, 'admin/realms/realm/groups']
        ];
    }

    /**
     * @test
     */
    public function create()
    {
        $groupCreatedResponse = $this->createResponseMock();
        $groupCreatedResponse->expects($this->once())->method('getHeader')
            ->with('Location')
            ->willReturn(['any-url'])
        ;
        $groupResponse = $this->createResponseMock('{"id":123,"name":"foo1"}');
        $this->apiClientMock->expects($this->any())->method('makeJsonRequest')
            ->willReturnOnConsecutiveCalls($groupCreatedResponse, $groupResponse)
        ;

        $user = $this->groupApi->create(new GroupRequest([]));

        $this->assertEquals(123, $user->getId());
        $this->assertEquals('foo1', $user->getName());
    }

    /**
     * @test
     */
    public function get()
    {
        $result = '{"id":123,"name":"foo1"}';
        $responseMock = $this->createResponseMock($result);
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with('GET', 'admin/realms/realm/groups/123', 'token')
            ->willReturn($responseMock)
        ;

        $group = $this->groupApi->get(123);

        $this->assertEquals(123, $group->getId());
        $this->assertEquals('foo1', $group->getName());
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

        $this->groupApi->create(new GroupRequest([]));
    }

    /**
     * @test
     * @expectedException \Neospheres\Keycloak\Exceptions\GroupException
     */
    public function requestWithCommonError()
    {
        $this->apiClientMock->method('makeJsonRequest')
            ->willThrowException(new \Exception())
        ;

        $this->groupApi->create(new GroupRequest([]));
    }
}