<?php declare(strict_types=1);

namespace RestSample\Tests\SlimControllers;

use RestSample\App;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\SlimControllers\MoviesController as Controller;
use RestSample\Tests\PdoModels\MoviesModelTest as Model;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Test suite for \movies endpoints
 */
class MoviesControllerTest extends \PHPUnit\Framework\TestCase
{
    use \RestSample\Tests\SlimControllerTestTrait;

    public function setUp()
    {
        // Mocks environment to build request
        // NB we manually add parameters to request in tests since they
        // are normally parsed during app run
        $this->request = Request::createFromEnvironment(Environment::mock([
            'SERVER_NAME' => 'localhost:8080',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ]));

        // Instantiates controller
        $db = App::withConfig()->getDbConnection();
        $this->controller = new Controller($db);

        // Resets movies table before each test
        (new Model)->setUp();
    }

    /** Tests \movies GET endpoint **/

    /**
     * @test
     */
    public function GET_missing_resource_returns_404_error()
    {
        // Adds parameters to request
        $request = $this->request->withAttributes(['id' => '9']);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('No Movie for ID 9');

        // Fires controller method
        $this->controller->get($request, new Response());
    }

    /**
     * @test
     */
    public function GET_valid_resource_returns_data()
    {
        $response = new Response();

        // Mocks expected response
        // NB we expect normal JSON mimetype here since middleware
        // handles formatting after controller call
        $expected = $response
            ->withJson(self::$MOVIES_GET, 200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        // Adds parameters to request
        $request = $this->request->withAttributes(['id' => '1']);

        // Fires controller method
        $actual = $this->controller->get($request, $response);

        // Compares page contents
        // NB we can't compare responses directly as body references a
        // stream resource with unique ID
        $this->assertEquals((string) $expected->getBody(), (string) $actual->getBody());

        // Compares Content-Type header
        $this->assertEquals($expected->getHeaders(), $actual->getHeaders());

        // Compares HTTP status code
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
