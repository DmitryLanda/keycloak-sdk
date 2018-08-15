<?php

namespace Neospheres\Keycloak\API;

use Neospheres\Keycloak\Exceptions\HttpException;
use Neospheres\Keycloak\Exceptions\UserException;
use Neospheres\Keycloak\Http\ApiClient;
use Neospheres\Keycloak\Models\SearchUsersRequest;
use Neospheres\Keycloak\Models\User;
use Neospheres\Keycloak\Models\UserRequest;

class UserApi
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
     * @param SearchUsersRequest $search
     * @param string $token
     * @return User[]
     */
    public function search(SearchUsersRequest $search, $token)
    {
        $url = $this->getSearchUrl();
        if ($query = $search->buildQueryString()) {
            $url .= '?' . $query;
        }
        try {
            $response = $this->client->makeJsonRequest(
                'GET',
                $url,
                $token
            );
            $data = json_decode($response->getBody()->getContents(), true);

            $users = [];
            foreach ($data as $item) {
                $users[] = new User($item);
            }

            return $users;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param UserRequest $request
     * @param string $token
     * @return User
     * @throws HttpException
     * @throws UserException
     */
    public function create(UserRequest $request, $token)
    {
        $params = array_merge($request->toArray(), ['emailVerified' => true, 'enabled' => true]);

        try {
            //create new user
            $response = $this->client->makeJsonRequest(
                'POST',
                $this->getCreateUserUrl(),
                $token,
                $params
            );
            $userDataUrl = $response->getHeader('Location')[0] ?? null;

            //fetch data for created user
            $response = $this->client->makeJsonRequest(
                'GET',
                $userDataUrl,
                $token
            );
            $userData = json_decode($response->getBody()->getContents(), true);

            return new User($userData);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw UserException::failedToCreateUser($e);
        }
    }

    /**
     * @param string $userId
     * @param UserRequest $request
     * @param string $token
     * @return bool
     * @throws HttpException
     * @throws UserException
     */
    public function update($userId, UserRequest $request, $token)
    {
        try {
            $this->client->makeJsonRequest(
                'PUT',
                $this->getSingleUserUrl($userId),
                $token,
                $request->toArray(true)
            );

            return true;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw UserException::failedToUpdateUser($e);
        }
    }

    /**
     * @param string $userId
     * @param string $token
     * @param string|null $clientId
     * @param string|null $redirectUrl
     * @return bool
     * @throws HttpException
     * @throws UserException
     */
    public function requestPasswordReset($userId, $token, $clientId = null, $redirectUrl = null)
    {
        $url = $this->getUpdatePasswordEmailUrl($userId, $clientId, $redirectUrl);

        try {
            $this->client->makeJsonRequest(
                'PUT',
                $url,
                $token,
                ['UPDATE_PASSWORD']
            );

            return true;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw UserException::failedToSendResetPasswordEmail($e);
        }
    }

    /**
     * @param string $userId
     * @param string $token
     * @return User
     * @throws HttpException
     * @throws UserException
     */
    public function get($userId, $token)
    {
        $response = $this->client->makeJsonRequest(
            'GET',
            $this->getSingleUserUrl($userId),
            $token
        );

        $userData = json_decode($response->getBody()->getContents(), true);

        return new User($userData);
    }

    /**
     * @return string
     */
    private function getSearchUrl()
    {
        return sprintf(
            'admin/realms/%s/users',
            $this->realm
        );
    }

    /**
     * @return string
     */
    private function getCreateUserUrl()
    {
        return sprintf(
            'admin/realms/%s/users',
            $this->realm
        );
    }

    /**
     * @param string $userId
     *
     * @return string
     */
    protected function getSingleUserUrl($userId)
    {
        return sprintf(
            'admin/realms/%s/users/%s',
            $this->realm,
            $userId
        );
    }

    /**
     * @param $userId
     * @param string|null $client
     * @param string|null $redirectUrl
     * @return string
     */
    private function getUpdatePasswordEmailUrl($userId, $client, $redirectUrl)
    {
        $url = sprintf(
            'admin/realms/%s/users/%s/execute-actions-email',
            $this->realm,
            $userId
        );

        $options = [];
        if ($client) {
            $options['client_id'] = $client;
        }
        if ($redirectUrl) {
            $options['redirect_uri'] = $redirectUrl;
        }

        if ($query = http_build_query($options)) {
            $url .= '?' . http_build_query($options);
        }

        return $url;
    }
}