<?php

class Client
{
    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get($url)
    {
        $response = $this->httpClient->request('GET', $url);

        return [
            'statusCode' => $response->getStatusCode(),
            'body' => json_decode($response->getBody(), true)
        ];
    }
}
