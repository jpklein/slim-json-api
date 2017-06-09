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
    public function __construct()
    {
        parent::__construct();

        // Mocks callable function for Slim middleware execution
        $this->slimMiddlewareCallableMock = static function (Request $req, Response $res) {
            // Returns unaltered Response
            return $res;
        };
    }

    public function setUp()
    {
        $this->environmentMock = Environment::mock();
        $this->requestMock = Request::createFromEnvironment($this->environmentMock);
        $this->responseMock  = new Response();
    }

    /**
     * @test
     * Servers MUST send all JSON API data in response documents with
     * the header Content-Type: application/vnd.api+json without any
     * media type parameters.
     */
    public function testCompliantRequestReturnsUnalteredResponse()
    {
        $expected = $this->responseMock->withHeader('Content-Type', 'application/vnd.api+json');

        // Invokes middleware
        // @todo Moves middleware invocation to test setup?
        $middleware = new JsonApiResponsibilitiesMiddleware;
        $actual = $this->requestMock->withHeader('Content-Type', 'application/vnd.api+json');

        $actual = $middleware($actual, $this->responseMock, $this->slimMiddlewareCallableMock);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testRequestWithoutContentTypeThrowsException()
    {
        $this->expectExceptionCode(400);

        // Invokes middleware
        $actual = new JsonApiResponsibilitiesMiddleware;
        $actual = $actual($this->requestMock, $this->responseMock, $this->slimMiddlewareCallableMock);
    }

    /**
     * @test
     * Servers MUST respond with a 415 Unsupported Media Type status
     * code if a request specifies the header Content-Type: application/
     * vnd.api+json with any media type parameters.
     */
    public function testRequestWithContentTypeParametersThrowsException()
    {
        $this->expectExceptionCode(415);

        // Invokes middleware with extraneous media type parameters
        $middleware = new JsonApiResponsibilitiesMiddleware;
        $actual = $this->requestMock->withHeader('Content-Type', 'application/vnd.api+json; charset=utf-8');

        $actual = $middleware($actual, $this->responseMock, $this->slimMiddlewareCallableMock);
    }

    /**
     * @test
     * Servers MUST respond with a 406 Not Acceptable status code if a
     * request’s Accept header contains the JSON API media type and all
     * instances of that media type are modified with media type
     * parameters.
     */
    // public function testRequestWithoutBasicAcceptTypeThrowsException()
    // {
    //     $this->expectExceptionCode(406);

    //     // Invokes middleware with extraneous media type parameters
    //     $middleware = new JsonApiResponsibilitiesMiddleware;
    //     $actual = $this->requestMock
    //         ->withHeader('Content-Type', 'application/vnd.api+json')
    //         ->withHeader('Accept', 'application/vnd.api+json; charset=utf-8')
    //         ->withHeader('Accept', 'text/html, application/json, application/vnd.api+json;q=0.9');

    //     $actual = $middleware($actual, $this->responseMock, $this->slimMiddlewareCallableMock);
    // }
}
