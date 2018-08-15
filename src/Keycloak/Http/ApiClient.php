<?php
namespace Neospheres\Keycloak\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Neospheres\Keycloak\Exceptions\HttpException;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    /**
     * @var Client
     */
    protected $transport;

    public function __construct($baseUrl, Client $transport = null)
    {
        $this->transport = $transport ?? new Client(['base_uri' => $baseUrl]);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $formData
     * @return array
     * @throws HttpException
     */
    public function makeFormRequest($method, $url, array $formData = [])
    {
        try {
            $response = $this->transport->request($method, $url, ['form_params' => $formData]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw $this->convertException($e);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param $token string
     * @param array|null $body
     *
     * @return ResponseInterface
     */
    public function makeJsonRequest($method, $url, $token, array $body = null)
    {
        $options = [
            'headers' => ['Authorization' => "Bearer $token"]
        ];
        if ($body) {
            $options['json'] = $body;

        }
        try {
            return $this->transport->request($method, $url, $options);
        } catch (GuzzleException $e) {
            throw $this->convertException($e);
        }
    }

    /**
     * @param GuzzleException $e
     * @return HttpException
     */
    private function convertException(GuzzleException $e)
    {
        if ($e instanceof RequestException) {
            return HttpException::wrap($e);
        }

        return HttpException::requestFailed($e);
    }
}