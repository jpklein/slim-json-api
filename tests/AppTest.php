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
    }

    /**
     * @test
     */
    public function testRequestWithoutContentTypeReturnsException()
    {
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
    }
}
