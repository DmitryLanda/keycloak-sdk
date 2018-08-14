<?php
namespace Neospheres\Keycloak\Exceptions;

class GroupException extends KeycloakException
{
    public static function failedToCreate(\Exception $previous)
    {
        return new self('keycloak.group.failed_to_create', null, $previous);
    }

    public static function failedToAssignUser(\Exception $previous)
    {
        return new self('keycloak.group.failed_to_assign_user', null, $previous);
    }

    public static function failedToFind(\Exception $previous)
    {
        return new self('keycloak.group.failed_to_find', null, $previous);
    }
}