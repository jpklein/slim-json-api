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

        // Compares page contents
        $expected = '{"errors":{"detail":"Bad Request"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
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

    /** Tests \movies GET endpoint **/

    /**
     * @test
     */
    public function testGetRequestToInvalidMoviesEndpointReturnsError()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/9');
        $response = $this->client->getResponse();

        // Compares page contents
        $expected = '{"errors":{"detail":"No Movie for ID 9"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
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

        // Compares page contents
        $expected = '{"data":[{"type":"movies","id":"1","attributes":{"name":"Avatar"}}]}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /** Tests \movieratings GET endpoint **/

    /**
     * @test
     */
    public function testGetRequestToInvalidMovieratingsEndpointReturnsError()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/9');
        $response = $this->client->getResponse();

        // Compares page contents
        $expected = '{"errors":{"detail":"No MovieRating for Movie ID 9"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testGetRequestToValidMovieratingsEndpointReturnsData()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/1');
        $response = $this->client->getResponse();

        // Compares page contents
        $expected = '{"data":[{"type":"movieratings","id":"1","attributes":{"average_rating":"4","total_ratings":"3"},"relationships":{"movies":{"data":{"type":"movies","id":"1"}}}}]}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /** Tests \movieratings POST endpoint **/

    /**
     * @test
     */
    public function testInvalidPostRequestToMovieratingsEndpointReturnsError()
    {
        // Resets the movieratings table
        (new PdoModels\MovieratingsModelTest)->setUp();

        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Sends the request with non-JSON POST data
        $this->client->request('POST', 'http://localhost:8080/movieratings', ['movie_id' => '2', 'average_rating' => '5', 'total_ratings' => '1']);
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // @todo Verifies that post data is JSON-formatted
        // $this->client->request('POST', 'http://localhost:8080/movieratings', ["data" => ["type" => "movieratings", "attributes" => ["average_rating" => "5", "total_ratings" => "1"], "relationships" => ["movies" => ["data" => ["type" => "movies", "id" => "2"]]]]]);
        // Compares page contents
        // $actual = ($this->client->getResponse())->getContent();
        // $this->assertEquals($expected, $actual);

        // Sends the request without required root member
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request as array of data items
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":[{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}]}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request without movie data
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request data without type parameter
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request data with invalid related type parameter
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movie","id":"2"}}}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request data with missing movie id
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies"}}}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request data with invalid attribute
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5 stars","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}}');
        // Compares page contents
        $actual = ($this->client->getResponse())->getContent();
        $this->assertEquals($expected, $actual);

        // Sends the request data without required attribute
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}}');
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testPostRequestToExistingMovieratingsEndpointReturnsError()
    {
        // Sends the request with JSON data in POST
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"4"},"relationships":{"movies":{"data":{"type":"movies","id":"1"}}}}}');
        $response = $this->client->getResponse();

        // Compares page contents
        $expected = '{"errors":{"detail":"MovieRating already exists for Movie ID 1"}}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 409;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testPostRequestToValidMovieratingsEndpointReturnsData()
    {
        // Resets auto increment on the movieratings table
        (new PdoModels\MovieratingsModelTest)->setUp();

        // Sends the request with JSON data in POST
        $this->client->request('POST', 'http://localhost:8080/movieratings', [], [], [], '{"data":{"type":"movieratings","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}}');
        $response = $this->client->getResponse();

        // Compares page contents
        $expected = '{"data":[{"type":"movieratings","id":"2","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}]}';
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }
}
