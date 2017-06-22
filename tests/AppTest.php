<?php declare(strict_types=1);

namespace RestSample\Tests;

use Goutte\Client;

class AppTest extends \PHPUnit\Framework\TestCase
{
    use \RestSample\Tests\SlimControllerTestTrait;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Client();

        // Sets the Content-Type header required by JSON API spec
        $this->client->setClient(new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/vnd.api+json']
        ]));
    }

    public function setUp()
    {
        // Resets the movieratings table on each test
        (new PdoModels\MovieratingsModelTest)->setUp();

        // Resets the usermovieratings table on each test
        (new PdoModels\UsermovieratingsModelTest)->setUp();
    }

    /**
     * @test
     */
    public function request_without_content_type_returns_400()
    {
        // Removes the Content-Type header
        $this->client->setClient(new \GuzzleHttp\Client());

        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);

        // Resets the Content-Type header
        $this->client->setClient(new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/vnd.api+json']
        ]));
    }

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function request_undefined_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/null');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \movies GET endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function GET_invalid_movies_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/null');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_undefined_movies_endpoint_returns_404()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"No Movie for ID 9"}}';

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/9');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_movies_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = json_encode(self::$MOVIES_GET);

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movies/1');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \movieratings GET endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function GET_invalid_movieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/null');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_undefined_movieratings_endpoint_returns_404()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"No MovieRating for Movie ID 9"}}';

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/9');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_movieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = json_encode(self::$MOVIERATINGS_GET);

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/movieratings/1');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \movieratings POST endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function POST_invalid_movieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings/null', self::$MOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function POST_undefined_movieratings_endpoint_returns_405()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Not Allowed"}}';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings/1', self::$MOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 405;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:60
     * @todo Verifies POST data is JSON-formatted
     */
    public function POST_nonconformant_movierating_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ['movie_id' => '2', 'average_rating' => '5', 'total_ratings' => '1'];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:60
     */
    public function POST_movierating_without_root_node_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST['data'];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:60
     */
    public function POST_movierating_in_array_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ["data" => [self::$MOVIERATINGS_POST['data']]];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:61
     */
    public function POST_movierating_without_subdata_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        unset($body['data']['relationships']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:65
     */
    public function POST_movierating_without_type_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST['data'];
        unset($body['type']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:66
     */
    public function POST_movierating_with_wrong_subtype_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        $body['data']['relationships']['movies']['data']['type'] = 'null';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:73
     */
    public function POST_movierating_without_subdata_id_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        unset($body['data']['relationships']['movies']['data']['id']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:74
     */
    public function POST_movierating_with_invalid_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        $body['data']['attributes']['average_rating'] = 'null';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:75
     */
    public function POST_movierating_without_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        unset($body['data']['attributes']['total_ratings']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:96
     */
    public function POST_existing_movierating_returns_409()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"MovieRating already exists for Movie ID 1"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_POST;
        $body['data']['relationships']['movies']['data']['id'] = "1";

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 409;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function POST_movieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = '{"data":[{"type":"movieratings","id":"2","attributes":{"average_rating":"5","total_ratings":"1"},"relationships":{"movies":{"data":{"type":"movies","id":"2"}}}}]}';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/movieratings', self::$MOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \movieratings PATCH endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function PATCH_invalid_movieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/null', self::$MOVIERATINGS_PATCH);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function PATCH_undefined_movieratings_endpoint_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/9');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:120
     * @todo Verifies PATCH data is JSON-formatted
     */
    public function PATCH_nonconformant_movierating_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ['movie_id' => '1', 'average_rating' => '5', 'total_ratings' => '4'];

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:120
     */
    public function PATCH_movierating_without_root_node_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH['data'];

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:121
     */
    public function PATCH_movierating_without_subdata_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        unset($body['data']['relationships']['movies']);

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:125
     */
    public function PATCH_movierating_with_invalid_type_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        $body['data']['type'] = 'null';

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:126
     */
    public function PATCH_movierating_without_subtype_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        unset($body['data']['relationships']['movies']['data']['type']);

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:127
     */
    public function PATCH_movierating_with_wrong_subdata_id_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        $body['data']['relationships']['movies']['data']['id'] = "2";

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:127
     */
    public function PATCH_movierating_with_invalid_subdata_id_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        $body['data']['relationships']['movies']['data']['id'] = "null";

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\MovieratingsController:135
     */
    public function PATCH_movierating_with_empty_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$MOVIERATINGS_PATCH;
        $body['data']['attributes']['total_ratings'] = 0;

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function PATCH_movieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = '{"data":[{"type":"movieratings","id":"1","attributes":{"average_rating":"5","total_ratings":"4"},"relationships":{"movies":{"data":{"type":"movies","id":"1"}}}}]}';

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/movieratings/1', self::$MOVIERATINGS_PATCH);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \usermovieratings GET endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function GET_invalid_usermovieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/usermovieratings/1/movies/null');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_undefined_usermovieratings_endpoint_returns_404()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"No UserMovieRating for Movie ID 9"}}';

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/usermovieratings/1/movies/9');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GET_usermovieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = json_encode(self::$USERMOVIERATINGS_GET);

        // Sends the request
        $this->client->request('GET', 'http://localhost:8080/usermovieratings/1/movies/1');

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \usermovieratings POST endpoint
     */

    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function POST_invalid_usermovieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings/1/movies/null', self::$USERMOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function POST_undefined_usermovieratings_endpoint_returns_405()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Not Allowed"}}';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings/1/movies/1', self::$USERMOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 405;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:61
     * @todo Verifies POST data is JSON-formatted
     */
    public function POST_nonconformant_usermovierating_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ['user_id' => '1', 'movie_id' => '2', 'rating' => '5'];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:61
     */
    public function POST_usermovierating_without_root_node_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST['data'];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:61
     */
    public function POST_usermovierating_in_array_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ["data" => [self::$USERMOVIERATINGS_POST['data']]];

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:62
     */
    public function POST_usermovierating_without_subdata_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        unset($body['data']['relationships']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:67
     */
    public function POST_usermovierating_with_invalid_type_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST['data'];
        $body['data']['type'] = "null";

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:69
     */
    public function POST_usermovierating_without_subtype_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        unset($body['data']['relationships']['movies']['data']['type']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:86
     */
    public function POST_usermovierating_with_empty_subdata_id_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        $body['data']['relationships']['movies']['data']['id'] = "0";

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:86
     */
    public function POST_usermovierating_with_invalid_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        $body['data']['attributes']['rating'] = "null";

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:78
     */
    public function POST_usermovierating_without_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        unset($body['data']['attributes']['rating']);

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:99
     */
    public function POST_existing_usermovierating_returns_409()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"UserMovieRating already exists for Movie ID 1"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_POST;
        $body['data']['relationships']['movies']['data']['id'] = "1";

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 409;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function POST_usermovieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = '{"data":[{"type":"usermovieratings","id":"4","attributes":{"rating":"5"},"relationships":{"users":{"data":{"type":"users","id":"1"}},"movies":{"data":{"type":"movies","id":"2"}}}}]}';

        // Sends the request
        $this->client->request('POST', 'http://localhost:8080/usermovieratings', self::$USERMOVIERATINGS_POST);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests \usermovieratings PATCH endpoint
     */


    /**
     * @test
     * @todo Modifies default Slim 404 to meet JSON API spec
     */
    public function PATCH_invalid_usermovieratings_endpoint_returns_404()
    {
        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/null', self::$USERMOVIERATINGS_PATCH);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares HTTP status code
        $expected = 404;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function PATCH_undefined_usermovieratings_endpoint_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH['data'];

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/9', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:120
     * @todo Verifies PATCH data is JSON-formatted
     */
    public function PATCH_nonconformant_usermovierating_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = ['user_id' => '1', 'movie_id' => '1', 'rating' => '5'];

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:120
     */
    public function PATCH_usermovierating_without_root_node_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH['data'];

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:121
     */
    public function PATCH_usermovierating_without_subdata_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH;
        unset($body['data']['relationships']);

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:125
     */
    public function PATCH_usermovierating_with_invalid_type_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH;
        $body['data']['type'] = 'null';

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:126
     */
    public function PATCH_usermovierating_without_subtype_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH;
        unset($body['data']['relationships']['users']['data']['type']);

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:127
     */
    public function PATCH_usermovierating_with_invalid_subdata_id_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH;
        $body['data']['relationships']['movies']['data']['id'] = "null";

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:135
     * @todo Runs input validation before Request::getParsedBody()?
     */
    // public function PATCH_usermovierating_with_invalid_attribute_returns_400()
    // {
    //     // Mocks expected response
    //     $expected = '{"errors":{"detail":"Bad Request"}}';
    //
    //     // Creates malformed post data
    //     $body = self::$USERMOVIERATINGS_PATCH;
    //     $body['data']['attributes']['rating'] = true;
    //
    //     // Sends the request
    //     $this->client->request('PATCH', 'http://localhost:8080/// usermovieratings/1/movies/1', $body);
    //
    //     // Fetches the response
    //     $response = $this->client->getResponse();
    //
    //     // Compares page contents
    //     $actual = $response->getContent();
    //     $this->assertEquals($expected, $actual);
    //
    //     // Compares Content-Type header
    //     $expected = 'application/vnd.api+json';
    //     $actual = $response->getHeaders()['Content-Type'][0];
    //     $this->assertEquals($expected, $actual);
    //
    //     // Compares HTTP status code
    //     $expected = 400;
    //     $actual = $response->getStatus();
    //     $this->assertEquals($expected, $actual);
    // }

    /**
     * @test
     * @see \RestSample\SlimControllers\UsermovieratingsController:135
     */
    public function PATCH_usermovierating_without_attribute_returns_400()
    {
        // Mocks expected response
        $expected = '{"errors":{"detail":"Bad Request"}}';

        // Creates malformed post data
        $body = self::$USERMOVIERATINGS_PATCH;
        unset($body['data']['attributes']['rating']);

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', $body);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 400;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function PATCH_usermovieratings_endpoint_returns_data()
    {
        // Mocks expected response
        $expected = '{"data":[{"type":"usermovieratings","id":"1","attributes":{"rating":"5"},"relationships":{"users":{"data":{"type":"users","id":"1"}},"movies":{"data":{"type":"movies","id":"1"}}}}]}';

        // Sends the request
        $this->client->request('PATCH', 'http://localhost:8080/usermovieratings/1/movies/1', self::$USERMOVIERATINGS_PATCH);

        // Fetches the response
        $response = $this->client->getResponse();

        // Compares page contents
        $actual = $response->getContent();
        $this->assertEquals($expected, $actual);

        // Compares Content-Type header
        $expected = 'application/vnd.api+json';
        $actual = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals($expected, $actual);

        // Compares HTTP status code
        $expected = 200;
        $actual = $response->getStatus();
        $this->assertEquals($expected, $actual);
    }
}
