<?php

namespace Neospheres\Keycloak\Models;

class TokenInfo
{
    use ArrayMapper;

    private $id;
    private $username;
    private $email;
    private $firstName;
    private $lastName;

    public function __construct(array $params)
    {
        $this->mapParams($params, [
            'sub' => 'id',
            'preferred_username' => 'username',
            'given_name' => 'first_name',
            'family_name' => 'last_name'
        ]);
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

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}