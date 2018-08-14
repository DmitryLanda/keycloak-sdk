<?php
namespace Neospheres\Keycloak\Exceptions;

class TokenException extends KeycloakException
{
    public static function failedToGetToken(\Exception $previous)
    {
        return new self('keycloak.token.failed_to_get', null, $previous);
    }
}