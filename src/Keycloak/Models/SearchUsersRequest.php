<?php

namespace Neospheres\Keycloak\Models;

class SearchUsersRequest
{
    use ArrayMapper;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $search;

    /**
     * @var int
     */
    private $limit = 100;

    /**
     * @var int
     */
    private $offset = 0;

    public function __construct(array $params)
    {
        $this->mapParams($params);
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return null|string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return array
     */
    public function buildQueryString()
    {
        $properties = array_filter(get_object_vars($this), function($value) {
            return $value !== null;
        });

        return http_build_query($properties);
    }
}