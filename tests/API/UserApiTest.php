<?php
namespace Neospheres\Keycloak\Test\API;

use Neospheres\Keycloak\API\UserApi;
use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Http\ApiClient;
use Neospheres\Keycloak\Models\SearchUsersRequest;
use Neospheres\Keycloak\Models\UserRequest;
use PHPUnit\Framework\MockObject\MockObject;
use Neospheres\Keycloak\Test\TestCase;

class UserApiTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $apiClientMock;

    /**
     * @var UserApi
     */
    private $userApi;

    protected function setUp()
    {
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userApi = new UserApi($this->apiClientMock, 'realm');
    }

    /**
     * @test
     * @dataProvider searchDataProvider
     * @param array $search
     * @param string $expectedUrl
     */
    public function search(array $search, $expectedUrl)
    {
        $result = '[
            {"id":123,"username":"foo1","email":"bar1@email.com","enabled":true},
            {"id":456,"username":"foo2","email":"bar2@email.com","enabled":false}
        ]';
        $responseMock = $this->createResponseMock($result);
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with('GET', $expectedUrl, 'token')
            ->willReturn($responseMock)
        ;

        $users = $this->userApi->search(new SearchUsersRequest($search), 'token');

        $this->assertCount(2, $users);

        $this->assertEquals(123, $users[0]->getId());
        $this->assertEquals('foo1', $users[0]->getUsername());
        $this->assertEquals('bar1@email.com', $users[0]->getEmail());

        $this->assertEquals(456, $users[1]->getId());
        $this->assertEquals('foo2', $users[1]->getUsername());
        $this->assertEquals('bar2@email.com', $users[1]->getEmail());
    }

    /**
     * @return array
     */
    public function searchDataProvider()
    {
        return [
            [['username' => 'foo'], 'admin/realms/realm/users?username=foo&limit=100&offset=0'],
            [['email' => 'foo@email.com'], 'admin/realms/realm/users?email=foo%40email.com&limit=100&offset=0'],
            [['first_name' => 'first'], 'admin/realms/realm/users?firstName=first&limit=100&offset=0'],
            [['last_name' => 'last'], 'admin/realms/realm/users?lastName=last&limit=100&offset=0'],
            [['search' => 'foo'], 'admin/realms/realm/users?search=foo&limit=100&offset=0'],
            [['search' => 'foo', 'first_name' => 'bar'], 'admin/realms/realm/users?firstName=bar&search=foo&limit=100&offset=0'],
            [['first_name' => 'foo', 'last_name' => 'bar'], 'admin/realms/realm/users?firstName=foo&lastName=bar&limit=100&offset=0'],
        ];
    }

    /**
     * @test
     */
    public function create()
    {
        $userCreatedResponse = $this->createResponseMock();
        $userCreatedResponse->expects($this->once())->method('getHeader')
            ->with('Location')
            ->willReturn(['any-url'])
        ;
        $userResponse = $this->createResponseMock('{"id":123,"username":"foo1","email":"bar1@email.com","enabled":true}');
        $this->apiClientMock->expects($this->any())->method('makeJsonRequest')
            ->willReturnOnConsecutiveCalls($userCreatedResponse, $userResponse)
        ;

        $user = $this->userApi->create(new UserRequest([]), 'token');

        $this->assertEquals(123, $user->getId());
        $this->assertEquals('foo1', $user->getUsername());
        $this->assertEquals('bar1@email.com', $user->getEmail());
    }

    /**
     * @test
     */
    public function update()
    {
        $userResponse = $this->createResponseMock();
        $this->apiClientMock->expects($this->any())->method('makeJsonRequest')
            ->willReturn($userResponse)
        ;

        $result = $this->userApi->update(123, new UserRequest([]), 'token');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function get()
    {
        $result = '{"id":123,"username":"foo1","email":"bar1@email.com","enabled":true}';
        $responseMock = $this->createResponseMock($result);
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with('GET', 'admin/realms/realm/users/123', 'token')
            ->willReturn($responseMock)
        ;

        $user = $this->userApi->get(123, 'token');

        $this->assertEquals(123, $user->getId());
        $this->assertEquals('foo1', $user->getUsername());
        $this->assertEquals('bar1@email.com', $user->getEmail());
    }

    /**
     * @test
     * @dataProvider resetPasswordDataProvider
     * @param string $id
     * @param string|null $clientId
     * @param string|null $redirectUrl
     * @param string $expectedUrl
     */
    public function requestPasswordReset($id, $clientId, $redirectUrl, $expectedUrl)
    {
        $userResponse = $this->createResponseMock();
        $this->apiClientMock->expects($this->once())->method('makeJsonRequest')
            ->with(
                'PUT',
                $expectedUrl,
                'token',
                ['UPDATE_PASSWORD']
            )
            ->willReturn($userResponse)
        ;

        $result = $this->userApi->requestPasswordReset($id, 'token', $clientId, $redirectUrl);

        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function resetPasswordDataProvider()
    {
        return [
            [123, null, null, 'admin/realms/realm/users/123/execute-actions-email'],
            [456, null, null, 'admin/realms/realm/users/456/execute-actions-email'],
            [123, 'foo', 'bar', 'admin/realms/realm/users/123/execute-actions-email?client_id=foo&redirect_uri=bar'],
        ];
    }

    /**
     * @test
     * @dataProvider errorDataProvider
     * @expectedException \Neospheres\Keycloak\Exceptions\HttpException
     *
     * @param string $method
     * @param array $args
     */
    public function requestWithHttpError($method, $args)
    {
        $this->apiClientMock->method('makeJsonRequest')
            ->willThrowException(new HttpException())
        ;

        call_user_func_array([$this->userApi, $method], $args);
    }

    /**
     * @test
     * @dataProvider errorDataProvider
     * @expectedException \Neospheres\Keycloak\Exceptions\UserException
     *
     * @param string $method
     * @param array $args
     */
    public function requestWithCommonError($method, $args)
    {
        $this->apiClientMock->method('makeJsonRequest')
            ->willThrowException(new \Exception())
        ;

        call_user_func_array([$this->userApi, $method], $args);
    }


    /**
     * @return array
     */
    public function errorDataProvider()
    {
        return [
            ['create', [new UserRequest([]), 'token']],
            ['update', [123, new UserRequest([]), 'token']],
            ['requestPasswordReset', [123, 'token', 'client', 'http://example.com']],
        ];
    }
}