<?php declare(strict_types=1);

namespace RestSample\Tests\SlimControllers;

// Aliases psr-7 objects
use RestSample\App;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\SlimControllers\UsermovieratingsController as Controller;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/** */
class UsermovieratingsControllerTest extends \PHPUnit\Framework\TestCase
{
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

        // Resets usermovieratings table before each test
        (new \RestSample\Tests\PdoModels\UsermovieratingsModelTest)->setUp();
    }

    /** Tests \usermovieratings GET endpoint **/

    private const EXPECTED_GET = [
        "data" => [[
            "type" => "usermovieratings",
            "id" => "1",
            "attributes" => [
                "rating" => "10"
            ],
            "relationships" => [
                "users" => [
                    "data" => [
                        "type" => "users",
                        "id" => "1"
                    ]
                ],
                "movies" => [
                    "data" => [
                        "type" => "movies",
                        "id" => "1"
                    ]
                ]
            ]
        ]]
    ];

    /**
     * @test
     */
    public function GET_missing_resource_returns_404_error()
    {
        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2']);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('No UserMovieRating for Movie ID 2');

        // Fires controller method
        $this->controller->get($request, new Response());
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
            ->withJson(self::EXPECTED_GET, 200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '1']);

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


    /** Tests \usermovieratings POST endpoint **/

    private const TEST_POST = [
        "data" => [
            "type" => "usermovieratings",
            "attributes" => [
                "rating" => "5"
            ],
            "relationships" => [
                "users" => [
                    "data" => [
                        "type" => "users",
                        "id" => "1"
                    ]
                ],
                "movies" => [
                    "data" => [
                        "type" => "movies",
                        "id" => "2"
                    ]
                ]
            ]
        ]
    ];

    /**
     * @test
     */
    public function POST_resource_without_root_node_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST['data'];

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_resource_without_relationships_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        unset($body['data']['relationships']);

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_resource_with_invalid_datatype_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        $body['data']['type'] = "invalid";

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_resource_without_subdatatype_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        unset($body['data']['relationships']['users']['data']['type']);

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_resource_with_invalid_subdata_id_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        $body['data']['relationships']['movies']['data']['id'] = '1';

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_with_invalid_parameter_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        $body['data']['attributes']['rating'] = true;

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_missing_required_parameter_returns_400_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        unset($body['data']['attributes']);

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_existing_resource_returns_409_error()
    {
        // Creates malformed post data
        $body = self::TEST_POST;
        $body['data']['relationships']['movies']['data']['id'] = "1";

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '1'])
            ->withParsedBody($body);

        // Describes expected exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(409);
        $this->expectExceptionMessage('UserMovieRating already exists for Movie ID 1');

        // Fires controller method
        $actual = $this->controller->post($request, new Response());
    }

    /**
     * @test
     */
    public function POST_new_resource_returns_data()
    {
        // Mocks expected response
        $response = new Response();
        $body = self::TEST_POST['data'];
        $body = array_slice($body, 0, 1, true) + ['id' => '4'] + array_slice($body, 1, null, true);
        $expected = $response
            ->withJson(['data' => [$body]], 200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        // Adds parameters to request
        $request = $this->request
            ->withAttributes(['user_id' => '1', 'movie_id' => '2'])
            ->withParsedBody(self::TEST_POST);

        // Fires controller method
        $actual = $this->controller->post($request, $response);

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
