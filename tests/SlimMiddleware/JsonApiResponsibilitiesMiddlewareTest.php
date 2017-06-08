<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\SlimMiddleware;

use RestSample\SlimMiddleware\JsonApiResponsibilitiesMiddleware;
use Slim\Http\Environment;
use Slim\Http\Response;
use Slim\Http\Request;

class JsonApiResponsibilitiesMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->environment = Environment::mock();
        $this->request = Request::createFromEnvironment($this->environment);
        $this->response = new Response();
    }

    /**
     * @test
     */
    public function testCompliantRequestReturnsUnalteredResponse()
    {
        $expected = $this->response;
        // $response = $this->withBody(new Body(fopen('php://temp', 'r+')));
        // $response->body->write($json = json_encode($data, $encodingOptions));

        // Ensure that the json encoding passed successfully
        // if ($json === false) {
        //     throw new \RuntimeException(json_last_error_msg(), json_last_error());
        // }

        $expected = $this->response->withHeader('Content-Type', 'application/vnd.api+json');
        // if (isset($status)) {
        //     return $responseWithJson->withStatus($status);
        // }
        // return $responseWithJson;


// var_dump($expected->getHeaders());die();
        $middleware = new JsonApiResponsibilitiesMiddleware;

        $actual = $middleware($this->request, $this->response, function ($req, $res) {
            return $res;
        });

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    // public function testNoncompliantRequestReturnsErrorResponse()
    // {

    // }
}
