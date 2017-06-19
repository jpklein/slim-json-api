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
    // // Includes DBUnit connection for testing
    // use \RestSample\Tests\PdoModelTestTrait {
    //     \RestSample\Tests\PdoModelTestTrait::setUp as traitSetUp;
    // }

    public function setUp()
    {
        // // Calls \PHPUnit\DbUnit\TestCaseTrait::setUp()
        // $this->traitSetUp();

        // // Injects PDO connection from DBUnit DefaultConnection object
        // $this->db = $this->getConnection()->getConnection();

        $this->db = \RestSample\App::withConfig()->getDbConnection();
    }

    /** Tests \usermovieratings GET endpoint **/

    /**
     * @test
     */
    public function GET_missing_resource_returns_404_error()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('No UserMovieRating for Movie ID 9');

        // Mocks environment to build request
        // NB we manually add arguments from URI to request since they
        // are normally parsed during app run
        $request = (Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/usermovieratings/9/movies/9',
            'SERVER_NAME' => 'localhost:8080',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ])))->withAttributes(['user_id' => '9', 'movie_id' => '9']);
        $controller = new ControllerUnderTest($this->db);
        $actual = $controller->get($request, new Response());
    }

    /**
     * @test
     */
    public function GET_valid_resource_returns_data()
    {
        // Mocks expected response
        // NB we expect normal JSON mimetype here since middleware
        // handles formatting after controller call
        $response = new Response();
        $expected = $response
            ->withJson(["data" => [["type" => "usermovieratings", "id" => "1", "attributes" => ["rating" => "10"], "relationships" => ["users" => ["data" => ["type" => "users", "id" => "1"]], "movies" => ["data" => ["type" => "movies", "id" => "1"]]]]]], 200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        // Mocks environment to build request
        // NB we manually add arguments from URI to request since they
        // are parsed during app run
        $request = (Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/usermovieratings/1/movies/1',
            'SERVER_NAME' => 'localhost:8080',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ])))->withAttributes(['user_id' => '1', 'movie_id' => '1']);
        $controller = new ControllerUnderTest($this->db);
        $actual = $controller->get($request, $response);

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
