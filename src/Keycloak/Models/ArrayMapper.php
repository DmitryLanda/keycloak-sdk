<?php

namespace Neospheres\Keycloak\Models;

trait ArrayMapper
{
    /**
     * @param array $params
     * @param array $transform
     */
    public function mapParams(array $params, array $transform = [])
    {
        foreach ($params as $property => $value) {
            $property = $this->transformProperty($property, $transform);
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    private function transformProperty($property, array $transform)
    {
        //apply property mapping if present
        if (array_key_exists($property, $transform)) {
            $property = $transform[$property];
        }

        //normalize property name: replace dashes with underscores
        $property = str_replace('-', '_', $property);
        //capitalize letters (using underscore as word separator)
        $property = ucwords($property, '_');
        //remove underscores to get property name in camel case
        $property = str_replace('_', '', $property);

        return lcfirst($property);
    }
}