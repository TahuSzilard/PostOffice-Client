<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Client.php'; // A Client osztály útvonala

class ClientTest extends TestCase
{
    private $httpClient;
    private $client;

    protected function setUp(): void    //Ez inicializálja a mockolt HTTP klienst és a Client osztályt
    {
        $this->httpClient = $this->createMock(\GuzzleHttp\Client::class);
        $this->client = new Client($this->httpClient);
    }

    public function testGetRequest()
    {
        $responseBody = json_encode(['status' => 'success', 'data' => ['name' => 'Test']]);
        $response = new \GuzzleHttp\Psr7\Response(200, [], $responseBody);

        $this->httpClient
            ->method('request')
            ->with('GET', '/test-endpoint')
            ->willReturn($response);

        $result = $this->client->get('/test-endpoint');

        $this->assertEquals(200, $result['statusCode']);
        $this->assertEquals('success', $result['body']['status']);
        $this->assertEquals('Test', $result['body']['data']['name']);
    }
    //Szimulál egy GET kérést egy /test-endpoint URL-re. 
    //Mockolt választ (Response) ad vissza, amelyet a Client osztály feldolgoz. 
    //Az assertEquals állítások ellenőrzik a válasz helyességét.
}
/*Eredmény:
    Ez a teszt ellenőrzi, hogy a Client osztály:
    Képes-e helyes HTTP GET kérést végrehajtani.
    Megfelelően tudja-e feldolgozni a JSON válaszokat.
*/