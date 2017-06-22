<?php declare(strict_types=1);

namespace RestSample\SlimControllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\PdoModels\UsermovieratingsModel as Model;

/**
 * Handles /usermovieratings endpoints
 */
class UsermovieratingsController
{
    use \RestSample\SlimControllerTrait;

    /**
     * Connects controller to model on demand
     *
     * @return RestSample\PdoModels\UsermovieratingsModel
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
        $user_id  = (int) $request->getAttribute('user_id');
        $movie_id = (int) $request->getAttribute('movie_id');

        // Calls model get method
        $result = $this->getModel()->getOneByPrimaryKeys($user_id, $movie_id);

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
        $userdata  = $data['relationships']['users']['data'];
        $moviedata = $data['relationships']['movies']['data'];

        // Validates JSON API resource definition
        switch (false) {
            case $data['type']      === 'usermovieratings':
            case $userdata['type']  === 'users':
            case $moviedata['type'] === 'movies':
                throw new Exception('Bad Request', 400);
                break;
        }

        // Validates required parameters
        foreach ([
            'user_id'  => $userdata['id'],
            'movie_id' => $moviedata['id'],
            'rating'   => $data['attributes']['rating']
        ] as $k => $v) {
            switch ($k) {
                // Tests integer values
                case 'user_id':
                case 'movie_id':
                case 'rating':
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
        $result = $this->getModel()->postNew($user_id, $movie_id, $rating);

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
        $user_id  = $request->getAttribute('user_id');
        $movie_id = $request->getAttribute('movie_id');

        // Throws error when array references are undefined
        set_error_handler(function () {
            throw new Exception('Bad Request', 400);
        });

        // Validates data format
        $data      = $request->getParsedBody()['data'];
        $userdata  = $data['relationships']['users']['data'];
        $moviedata = $data['relationships']['movies']['data'];

        // Validates JSON API resource definition
        switch (false) {
            case $data['type']      === 'usermovieratings':
            case $userdata['type']  === 'users':
            case $userdata['id']    === $user_id:
            case $moviedata['type'] === 'movies':
            case $moviedata['id']   === $movie_id:
                throw new Exception('Bad Request', 400);
                break;
        }

        // Validates additional required parameters
        foreach ([
            'rating' => $data['attributes']['rating']
        ] as $k => $v) {
            switch ($k) {
                // Tests integer values
                case 'rating':
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
        $result = $this->getModel()->patchByPrimaryKeys((int) $user_id, (int) $movie_id, $rating);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }
}
