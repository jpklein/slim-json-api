<?php declare(strict_types=1);

namespace RestSample\Tests\SlimControllers;

// Aliases psr-7 objects
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\SlimControllers\UsermovieratingsController as ControllerUnderTest;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/** */
class UsermovieratingsControllerTest extends \PHPUnit\Framework\TestCase
{
    /** Tests \usermovieratings GET endpoint **/

    /**
     * @test
     */
    public function GET_to_missing_resource_returns_HTTP_405()
    {
        $response = new Response();
        $expected = $response
            ->withJson(["errors" => ["detail" => "Bad Request"]], 400)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        // Mocks environment to build request
        $request = Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/usermovieratings/9/movies/9',
            'SERVER_NAME' => 'localhost:8080',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ]));
        $controller = new ControllerUnderTest;
        $actual = $controller($request, $response);

        // Compares page contents
        // NB we can't compare Responses directly: Body references a
        // stream resource with unique ID
        $this->assertEquals((string) $expected->getBody(), (string) $actual->getBody());

        // Compares Content-Type header
        $this->assertEquals($expected->getHeaders(), $actual->getHeaders());

        // Compares HTTP status code
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
