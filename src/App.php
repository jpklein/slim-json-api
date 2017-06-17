<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample;

// Aliases psr-7 objects
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\SlimHandlers\JsonApiErrorHandler;
use RestSample\SlimMiddleware\JsonApiResponsibilitiesMiddleware as JsonApiMiddleware;

class App
{
    private static $config;

    public function __construct()
    {
        define('APP_ENV', getenv('APPLICATION_ENV'));
        define('APP_ROOT', dirname(__DIR__));

        // Imports project settings
        if (APP_ENV && ($filepath = APP_ROOT.'/config/env/'.APP_ENV.'.php') && file_exists($filepath)) {
            require $filepath;
        }
        defined('APP_CONFIG') || require APP_ROOT.'/config/default.php';
    }

    public static function withConfig(): App
    {
        if (is_null(self::$config)) {
            self::$config = new self();
        }
        return self::$config;
    }

    public function getDbConnection(): \PDO
    {
        $dsn = 'mysql:host='.APP_CONFIG['db']['host'];
        $pdo = new \PDO($dsn, APP_CONFIG['db']['user'], APP_CONFIG['db']['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec(strtr('CREATE DATABASE IF NOT EXISTS ?;USE ?;', ['?' => APP_CONFIG['db']['dbname']]));
        return $pdo;
    }

    /**
     * Returns Slim dispatcher to handle incoming HTTP requests
     * @todo Persists individual ratings in usermovieratings table
     * @todo Validates input parameters in middleware
     * @todo Exposes OPTIONS endpoint
     *
     * @return \Slim\App
     */
    public function getRouter(): \Slim\App
    {
        // Autoloads Composer dependencies
        require '../vendor/autoload.php';

        // Creates Slim application
        $slim = new \Slim\App(['settings' => APP_CONFIG]);

        // Creates dependency injection container
        $dic = $slim->getContainer();

        // Establishes connection to mysql db
        $dic['db'] = $this->getDbConnection();

        // Overrides default Slim error handler
        $dic['errorHandler'] = function ($c) {
            return new JsonApiErrorHandler;
        };

        // Adds middleware for JSON API content negotiation
        $middleware = new JsonApiMiddleware;
        $slim->add($middleware);

        // Retrieves movie data given a unique ID
        $slim->get('/movies/{id}', function (Request $request, Response $response) {
            // Calls model get method
            $model = new PdoModels\MoviesModel($this->db);
            // @throws JsonApiException
            $result = $model->getOneById((int) $request->getAttribute('id'));
            // Formats output
            return $response->withJson(['data' => [$result]]);
        });

        // Retrieves overall movie rating based on all users' ratings
        $slim->get('/movieratings/{movie_id}', function (Request $request, Response $response) {
            // Calls model get method
            $model = new PdoModels\MovieratingsModel($this->db);
            // @throws JsonApiException
            $result = $model->getOneByMovieId((int) $request->getAttribute('movie_id'));
            // Formats output
            return $response->withJson(['data' => [$result]]);
        });

        // Accepts movie rating per user
        // @todo Limits spam requests to endpoint
        $slim->post('/movieratings', function (Request $request, Response $response) {
            $params = [];

            // Errors if array members referenced below are undefined
            set_error_handler(function () {
                throw new Exception('Bad Request', 400);
            });

            // Ensures correct input format
            $data = $request->getParsedBody()['data'];
            $subdata = $data['relationships']['movies']['data'];
            if ($data['type'] !== 'movieratings' || $subdata['type'] !== 'movies') {
                throw new Exception('Bad Request', 400);
            }

            // Sanitizes input parameters
            foreach ($data['attributes'] + $subdata as $k => $v) {
                switch ($k) {
                    case 'id':
                        $k = 'movie_id';
                        // Falls through to test integer value input
                    case 'average_rating':
                        // Falls through to test integer value input
                    case 'total_ratings':
                        // Ignores value unless it passes validation
                        if (!is_bool($v) && ($v = filter_var($v, FILTER_VALIDATE_INT)) !== false) {
                            $params[$k] = (int) $v;
                        }
                        break;
                }
            }

            // Validates required parameters
            if (!($params['movie_id'] && $params['average_rating'] && $params['total_ratings'])) {
                throw new Exception('Bad Request', 400);
            }

            // Allows other errors besides 400 to be returned
            restore_error_handler();

            // Calls model set method
            $model = new PdoModels\MovieratingsModel($this->db);
            // @throws JsonApiException
            $result = $model->postNew($params['movie_id'], $params['average_rating'], $params['total_ratings']);
            // Formats output
            return $response->withJson(['data' => [$result]]);
        });

        return $slim;
    }
}
