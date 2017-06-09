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
}
