<?php

namespace Neospheres\Keycloak\Models;

class GroupRequest
{
    use ArrayMapper;

    /**
     * @var string
     */
    private $name;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes ?? [];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name'   => $this->getName(),
            'attributes' => array_map(function($item) {
                return [$item];
            }, $this->getAttributes())
        ];
    }
}