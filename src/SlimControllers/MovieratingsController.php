<?php declare(strict_types=1);

namespace RestSample\SlimControllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\PdoModels\MovieratingsModel as Model;

class MovieratingsController
{
    use \RestSample\SlimControllerTrait;

    /**
     * Connects controller to model on demand
     *
     * @return RestSample\PdoModels\MovieratingsModel
     */
    final private function getModel()
    {
        return new Model(self::$db);
    }

    /**
     * @param  Request  $request
     * @param  Response $response
     *
     * @throws JsonApiException
     * @return Slim\Http\Response
     */
    public function get(Request $request, Response $response)
    {
        // Fetches URI parameters
        // NB there's no need sanitize arguments here. FastRoute
        // performs validation using regex pattern matching
        $movie_id = (int) $request->getAttribute('movie_id');

        // Calls model get method
        $result = $this->getModel()->getOneByMovieId($movie_id);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }

    /**
     * @param  Request  $request
     * @param  Response $response
     *
     * @throws JsonApiException
     * @return Slim\Http\Response
     */
    public function post(Request $request, Response $response)
    {
        // Throws error when array references are undefined
        set_error_handler(function () {
            throw new Exception('Bad Request', 400);
        });

        // Validates data format
        $data      = $request->getParsedBody()['data'];
        $moviedata = $data['relationships']['movies']['data'];

        // Validates JSON API resource definition
        switch (false) {
            case $data['type']      === 'movieratings':
            case $moviedata['type'] === 'movies':
                throw new Exception('Bad Request', 400);
                break;
        }

        // Validates required parameters
        foreach ([
            'movie_id'       => $moviedata['id'],
            'average_rating' => $data['attributes']['average_rating'],
            'total_ratings'  => $data['attributes']['total_ratings']
        ] as $k => $v) {
            switch ($k) {
                // Tests integer values
                case 'movie_id':
                case 'average_rating':
                case 'total_ratings':
                    // Throws error if any parameter fails validation
                    if (is_bool($v) || ($v = filter_var($v, FILTER_VALIDATE_INT)) === false || !$v) {
                        throw new Exception('Bad Request', 400);
                    }
                    // Creates vars in local closure with param names
                    ${$k} = (int) $v;
                    break;
            }
        }

        // Allows other errors besides 400 to be returned
        restore_error_handler();

        // Calls model set method
        $result = $this->getModel()->postNew($movie_id, $average_rating, $total_ratings);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }

    /**
     * @param  Request  $request
     * @param  Response $response
     *
     * @throws JsonApiException
     * @return Slim\Http\Response
     */
    public function patch(Request $request, Response $response)
    {
        // Fetches URI parameters
        $movie_id = $request->getAttribute('movie_id');

        // Throws error when array references are undefined
        set_error_handler(function () {
            throw new Exception('Bad Request', 400);
        });

        // Validates data format
        $data      = $request->getParsedBody()['data'];
        $moviedata = $data['relationships']['movies']['data'];

        // Validates JSON API resource definition
        switch (false) {
            case $data['type']      === 'movieratings':
            case $moviedata['type'] === 'movies':
            case $moviedata['id']   === $movie_id:
                throw new Exception('Bad Request', 400);
                break;
        }

        // Validates additional required parameters
        foreach ([
            'average_rating' => $data['attributes']['average_rating'],
            'total_ratings'  => $data['attributes']['total_ratings']
        ] as $k => $v) {
            switch ($k) {
                // Tests integer values
                case 'average_rating':
                case 'total_ratings':
                    // Throws error if any parameter fails validation
                    if (is_bool($v) || ($v = filter_var($v, FILTER_VALIDATE_INT)) === false || !$v) {
                        throw new Exception('Bad Request', 400);
                    }
                    // Creates vars in local closure with param names
                    ${$k} = (int) $v;
                    break;
            }
        }

        // Allows other errors besides 400 to be returned
        restore_error_handler();

        // Calls model set method
        $result = $this->getModel()->patchByMovieId((int) $movie_id, $average_rating, $total_ratings);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }
}
