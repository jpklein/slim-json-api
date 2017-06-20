<?php declare(strict_types=1);

namespace RestSample\SlimControllers;

// Aliases psr-7 objects
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\PdoModels\UsermovieratingsModel as Model;

/** */
class UsermovieratingsController //extends \RestSample\SlimController
{
    /**
     * @param  PDO $db
     */
    public function __construct(\PDO $db)
    {
        // Connects controller to model
        $this->model = new Model($db);
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
        $result = $this->model->getOneByPrimaryKeys($user_id, $movie_id);

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
        // Errors if array members referenced below are undefined
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
        $parameters = ['user_id' => $userdata['id'], 'movie_id' => $moviedata['id']] + $data['attributes'];
        foreach ($parameters as $k => $v) {
            switch ($k) {
                case 'user_id':
                case 'movie_id':
                case 'rating':
                    // Errors unless all parameters pass validation
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
        $result = $this->model->postNew($user_id, $movie_id, $rating);

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

        // Errors if array members referenced below are undefined
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

        // Validates required parameters
        foreach ($data['attributes'] as $k => $v) {
            switch ($k) {
                case 'rating':
                    // Errors unless it passes validation
                    if (is_bool($v) || ($v = filter_var($v, FILTER_VALIDATE_INT)) === false) {
                        throw new Exception('Bad Request', 400);
                    }
                    ${$k} = (int) $v;
                    break;
            }
        }

        // Allows other errors besides 400 to be returned
        restore_error_handler();

        // Calls model set method
        $result = $this->model->patchByPrimaryKeys((int) $user_id, (int) $movie_id, $rating);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }
}
