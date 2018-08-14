<?php

namespace Neospheres\Keycloak\API;

use Neospheres\Keycloak\Exceptions\GroupException;
use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Models\Group;
use Neospheres\Keycloak\Models\GroupRequest;
use Neospheres\Keycloak\Http\ApiClient;

class GroupApi
{
    /**
     * @var ApiClient
     */
    private $client;

    /**
     * @var string
     */
    private $realm;

    /**
     * @param ApiClient $client
     * @param string $realm
     */
    public function __construct(ApiClient $client, $realm)
    {
        $this->client = $client;
        $this->realm = $realm;
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @return bool
     * @throws HttpException
     * @throws GroupException
     */
    public function addUser($userId, $groupId)
    {
        try {
            $this->client->makeJsonRequest(
                'PUT',
                $this->getAddUserToGroupUrl($userId, $groupId),
                $this->client->authorizeAsAdmin()
            );

            return true;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw GroupException::failedToAssignUser($e);
        }
    }

    /**
     * @param string|null $userId
     * @return Group[]
     */
    public function search($userId = null)
    {
        if ($userId) {
            $url = $this->getUserGroupsUrl($userId);
        } else {
            $url = $this->getAllGroupsUrl();
        }

        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $url,
                $this->client->authorizeAsAdmin()
            );

            $groupsData = json_decode($response->getBody()->getContents(), true);

            $groups = [];
            foreach ($groupsData as $data) {
                $groups[] = new Group($data);
            }

            return $groups;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $groupId
     * @return Group
     * @throws HttpException
     * @throws GroupException
     */
    public function get($groupId)
    {
        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $this->getSingleGroupUrl($groupId),
                $this->client->authorizeAsAdmin()
            );

            $data = json_decode($response->getBody()->getContents(), true);

            return new Group($data);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw GroupException::failedToFind($e);
        }
    }

    /**
     * @param GroupRequest $request
     * @return Group
     * @throws HttpException
     * @throws GroupException
     */
    public function create(GroupRequest $request)
    {
        try {
            //create group
            $response = $this->client->makeJsonRequest(
                'POST',
                $this->getAllGroupsUrl(),
                $this->client->authorizeAsAdmin(),
                $request->toArray()
            );
            $groupUrl = $response->getHeader('Location')[0] ?? null;

            //fetch created group back
            $response = $this->client->makeJsonRequest(
                'GET',
                $groupUrl,
                $this->client->authorizeAsAdmin()
            );
            $data = json_decode($response->getBody()->getContents(), true);

            return new Group($data);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw GroupException::failedToCreate($e);
        }
    }

    /**
     * @param string $userId
     * @param string $groupId
     *
     * @return string
     */
    private function getAddUserToGroupUrl($userId, $groupId)
    {
        return sprintf(
            'admin/realms/%s/users/%s/groups/%s',
            $this->realm,
            $userId,
            $groupId
        );
    }

    /**
     * @param string $userId
     *
     * @return string
     */
    private function getUserGroupsUrl($userId)
    {
        return sprintf(
            'admin/realms/%s/users/%s/groups',
            $this->realm,
            $userId
        );
    }

    /**
     * @return string
     */
    private function getAllGroupsUrl()
    {
        return sprintf(
            'admin/realms/%s/groups',
            $this->realm
        );
    }

    /**
     * @param string $groupId
     *
     * @return string
     */
    private function getSingleGroupUrl($groupId)
    {
        return sprintf(
            'admin/realms/%s/groups/%s',
            $this->realm,
            $groupId
        );
    }
}