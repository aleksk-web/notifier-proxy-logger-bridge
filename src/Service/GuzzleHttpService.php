<?php


namespace App\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleHttpService
{
    /**
     * @var Client $client
     */
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    const REQUEST_METHOD_POST = "POST";

    const KEY_BODY = "body";

    /**
     * @param string $calledUrl
     * @param array $dataBag
     * @return string
     * @throws GuzzleException
     */
    public function sendPostRequest(string $calledUrl, array $dataBag = []): string
    {
        $response = $this->client->request(self::REQUEST_METHOD_POST, $calledUrl, [
            self::KEY_BODY => $dataBag,
        ]);

        return $response->getBody()->getContents();
    }

}