<?php

namespace Neospheres\Keycloak\Models;

class TokenInfo
{
    use ArrayMapper;

    private $id;
    private $username;
    private $email;

    public function __construct(array $params)
    {
        $this->mapParams($params, ['sub' => 'id', 'preferred_username' => 'username']);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}