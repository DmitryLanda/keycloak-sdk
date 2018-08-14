<?php

namespace Neospheres\Keycloak\Models;

class UserRequest
{
    use ArrayMapper;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $params)
    {
        $this->mapParams($params);
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
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
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

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes ?? [];
    }

    /**
     * @param bool $notEmptyOnly
     * @return array
     */
    public function toArray($notEmptyOnly = false)
    {
        $data = [
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'enabled' => $this->isEnabled(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'attributes' => $this->getAttributes()
        ];
        $result = $data;

        if ($notEmptyOnly) {
            $result = [];
            foreach ($data as $key => $value) {
                if ((is_array($value) && $value !== []) || $value !== null) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}