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
        $expected = $this->response->withHeader('Content-Type', 'application/vnd.api+json');

        $middleware = new JsonApiResponsibilitiesMiddleware;
        $actual = $this->request->withHeader('Content-Type', 'application/vnd.api+json');

        $actual = $middleware($actual, $this->response, function ($req, $res) {
            // @todo Refactors slimMiddlewareCallableMock
            return $res;
        });

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testNoncompliantRequestThrowsException()
    {
        $this->expectExceptionCode(400);

        // Invokes middleware
        // @todo Moves middleware invocation to test setup
        $actual = new JsonApiResponsibilitiesMiddleware;
        $actual = $actual($this->request, $this->response, function ($req, $res) {
            return $res;
        });

        $this->assertEquals($expected, $actual);
    }
}
