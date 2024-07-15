<?php

namespace App\service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchWords(int $lenght): array
    {
        $response = $this->client->request(
            'GET',
            'https://trouve-mot.fr/api/sizemin/'.$lenght.'/30' 
        );

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch words from API');
        }

        return $response->toArray();
    }
}