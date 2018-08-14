<?php

namespace Neospheres\Keycloak\Models;

class Group
{
    use ArrayMapper;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * KeycloakGroup constructor.
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
    public function getName()
    {
        return $this->name;
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
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getConnectionData()
    {
        $attributes = $this->getAttributes();

        return [
            'host'     => $attributes['db_host'] ?? null,
            'port'     => $attributes['db_port'] ?? null,
            'database' => $attributes['db_name'] ?? null,
            'username' => $attributes['db_user'] ?? null,
            'password' => $attributes['db_password'] ?? null,
        ];
    }
}