<?php declare(strict_types=1);

namespace RestSample;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\SlimControllers\MoviesController;
use RestSample\SlimControllers\MovieratingsController;
use RestSample\SlimControllers\UsermovieratingsController;
use RestSample\SlimHandlers\JsonApiErrorHandler;
use RestSample\SlimMiddleware\JsonApiResponsibilitiesMiddleware as JsonApiMiddleware;

/**
 * Main application
 *
 * Generates a Slim router to handle incoming requests.
 */
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

        // Overrides default Slim error handler
        $dic['notAllowedHandler'] = function ($c) {
            return function (Request $request, Response $response, array $methods) use ($c) {
                // @todo Logs attempts to access unsupported endpoints
                // $this->writeToErrorLog($exception);

                return $response
                    ->withJson(['errors' => ['detail' => 'Not Allowed']], 405)
                    ->withHeader('Content-Type', 'application/vnd.api+json')
                    ->withHeader('Allow', implode(', ', $methods));
            };
        };

        // Adds middleware for JSON API content negotiation
        $middleware = new JsonApiMiddleware;
        $slim->add($middleware);

        // @todo Limits spam requests to endpoint
        // @todo Checks authentication/authorization

        // Displays movie data
        $dic['MoviesController'] = function ($c) {
            return new MoviesController($c->db);
        };
        // Defines movies endpoints
        $slim->get('/movies/{id:[0-9]+}', 'MoviesController:get');

        // Displays overall movie rating based on all user ratings
        $dic['MovieratingsController'] = function ($c) {
            return new MovieratingsController($c->db);
        };
        // Defines movieratings endpoints
        $slim->group('/movieratings', function () {
            $this->post('', 'MovieratingsController:post');

            $pattern = '/{movie_id:[0-9]+}';
            $this->get($pattern, 'MovieratingsController:get');
            $this->patch($pattern, 'MovieratingsController:patch');
        });

        // Displays a user's rating of a movie
        $dic['UsermovieratingsController'] = function ($c) {
            return new UsermovieratingsController($c->db);
        };
        // Defines usermovieratings endpoints
        $slim->group('/usermovieratings', function () {
            $this->post('', 'UsermovieratingsController:post');

            $pattern = '/{user_id:[0-9]+}/movies/{movie_id:[0-9]+}';
            $this->get($pattern, 'UsermovieratingsController:get');
            $this->patch($pattern, 'UsermovieratingsController:patch');
        });

        return $slim;
    }
}
