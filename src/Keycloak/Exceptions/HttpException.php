<?php

namespace Neospheres\Keycloak\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class HttpException extends KeycloakException
{
    /**
     * @param RequestException $e
     * @return HttpException
     */
    public static function wrap(RequestException $e)
    {
        $error = json_decode($e->getResponse()->getBody()->getContents(), true);
        $message = $error['errorMessage'] ?? null;

        return new self(
            $message,
            $e->getResponse()->getStatusCode(),
            $e
        );
    }

    /**
     * @param GuzzleException $e
     * @return HttpException
     */
    public static function requestFailed(GuzzleException $e)
    {
        return new self(
            'keycloak.http.request_failed',
            null,
            $e
        );
    }
}