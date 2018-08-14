<?php
namespace Neospheres\Keycloak\Exceptions;

class UserException extends KeycloakException
{
    public static function failedToCreateUser(\Exception $previous)
    {
        return new self('keycloak.user.failed_to_create', null, $previous);
    }

    public static function failedToUpdateUser(\Exception $previous)
    {
        return new self('keycloak.user.failed_to_update', null, $previous);
    }

    public static function failedToSendResetPasswordEmail(\Exception $previous)
    {
        return new self('keycloak.user.failed_to_send_reset_password_email', null, $previous);
    }
}