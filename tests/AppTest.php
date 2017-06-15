<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests;

use Goutte\Client;

class AppTest extends \PHPUnit\Framework\TestCase
{
    public function __construct()
    {
        parent::__construct();

        $this->client = new Client();

        // Sets the Content-Type header required by JSON API spec
        $this->client->setClient(new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/vnd.api+json']
        ]));
    }

    /**
     * @test
     */
    public function testRequestWithoutContentTypeReturnsException()
    {
        // Removes the Content-Type header
        $this->client->setClient(new \GuzzleHttp\Client());

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080');
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares page contents
        $expected = '{"errors":{"detail":"Bad Request"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Resets the Content-Type header
        $this->client->setClient(new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/vnd.api+json']
        ]));
    }

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function testRequestForUndefinedEndpointReturnsException()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080');
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /** Tests \movies endpoint **/

    /**
     * @test
     */
    public function testGetRequestToInvalidMoviesEndpointReturnsError()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/9');
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares page contents
        $expected = '{"errors":{"detail":"No Movie for ID 9"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testGetRequestToValidMoviesEndpointReturnsData()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/1');
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares page contents
        $expected = '{"data":[{"type":"movies","id":"1","attributes":{"name":"Avatar"}}]}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);
    }

    /** Tests \movieratings endpoint **/

    /**
     * @test
     */
    public function testGetRequestToMovieratingsEndpointReturnsData()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/1');
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares page contents
        $expected = '{"data":[{"type":"movieratings","id":"1","attributes":{"average_rating":"4","total_ratings":"3"},"relationships":{"movies":{"data":{"type":"movies","id":"1"}}}}]}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);
    }
}
