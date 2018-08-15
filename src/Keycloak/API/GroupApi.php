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
     * @param string $token
     * @return bool
     * @throws HttpException
     * @throws GroupException
     */
    public function addUser($userId, $groupId, $token)
    {
        try {
            $this->client->makeJsonRequest(
                'PUT',
                $this->getAddUserToGroupUrl($userId, $groupId),
                $token
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
     * @param string $token
     * @return Group[]
     */
    public function search($userId = null, $token)
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
                $token
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
     * @param string $token
     * @return Group
     * @throws HttpException
     * @throws GroupException
     */
    public function get($groupId, $token)
    {
        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $this->getSingleGroupUrl($groupId),
                $token
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
     * @param $userId
     * @param string $token
     * @return Group[]
     * @throws HttpException
     * @throws GroupException
     */
    public function getUserGroups($userId, $token)
    {
        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $this->getUserGroupsUrl($userId),
                $token
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $groups = [];
            foreach ($data as $item) {
                $groups[] = new Group($item);
            }

            return $groups;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw GroupException::failedToFind($e);
        }
    }

    /**
     * @param GroupRequest $request
     * @param string $token
     * @return Group
     * @throws HttpException
     * @throws GroupException
     */
    public function create(GroupRequest $request, $token)
    {
        try {
            //create group
            $response = $this->client->makeJsonRequest(
                'POST',
                $this->getAllGroupsUrl(),
                $token,
                $request->toArray()
            );
            $groupUrl = $response->getHeader('Location')[0] ?? null;

            //fetch created group back
            $response = $this->client->makeJsonRequest(
                'GET',
                $groupUrl,
                $token
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