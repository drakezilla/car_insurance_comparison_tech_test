<?php

namespace App\Provider;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AxaAdapter
{
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function handle(string $provider, array $data): ResponseInterface
    {
        $response = $this->httpClient->request('GET', $data["url"], [
                'user_data' => $provider,
                'timeout' => 10,
                'max_duration' => 10,
        ]);

        return $response;
    }

    public static function transform(ResponseInterface $response)
    {
        die("hola");
    }
}
