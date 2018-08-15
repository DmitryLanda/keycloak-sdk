<?php

namespace Neospheres\Keycloak\Models;

use Carbon\Carbon;

class User
{
    use ArrayMapper;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var bool
     */
    private $emailVerified;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var integer
     */
    private $createdTimestamp;

    /**
     * @var array
     */
    private $attributes;

    /**
     * KeycloakUser constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->mapParams($params);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return Carbon::createFromTimestamp($this->createdTimestamp);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [];

        foreach ($this->attributes as $key => $attribute) {
            $attributes[$key] = reset($attribute);
        }

        return $attributes;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
}
