<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests;

use JonnyW\PhantomJs\Client;

class AppTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->client = Client::getInstance();
    }

    // Returns an instance of PhantomJS Message to make JSON requests
    private function withClient()
    {
        return $this->client->getMessageFactory();
    }

    /**
     * @test
     * @see JonnyW\PhantomJs\Http\{Request|Response}
     */
    public function testRequestWithoutContentTypeReturnsException()
    {
        $request = $this->withClient()->createRequest('http://localhost:8080', 'GET');
        $response = $this->withClient()->createResponse();

        // Sends the request
        $this->client->send($request, $response);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getContentType();
        $this->assertEquals($expected, $actual);

        // @todo Compares page contents using PhantomJS?
        // note: $actual is null as long as handler returns response
        //       with JSON API header
        // @see  RestSample\SlimHandlers\JsonApiErrorHandler::__invoke()
        $expected = '{"errors":{"detail":"Bad Request"}}';
        $actual = $response->getContents();
        $this->assertEquals($expected, $actual);
    }
}
