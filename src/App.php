<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

namespace RestSample;

// Aliases psr-7 objects
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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
     * @todo Validates input parameters in middleware
     * @todo Exposes OPTIONS endpoint
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

        // Retrieves movie data given a unique ID
        $slim->get('/moviedata/{movie_id}', function (Request $request, Response $response) {
            try {
                $model = new PdoModels\MoviedataModel($this->db);
                $result = $model->getMovieDataById((int) $request->getAttribute('movie_id'));
            } catch (\Exception $e) {
                return $response->withJson($e->getMessage(), $e->getCode());
            }

            // Formats output
            $result->data = json_decode($result->serialized);
            unset($result->serialized);

            return $response->withJson($result);
        });

        // Retrieves overall movie rating based on all users' ratings
        $slim->get('/movierating/{movie_id}', function (Request $request, Response $response) {
            try {
                $model = new PdoModels\MovieratingModel($this->db);
                $result = $model->getMovieRatingById((int) $request->getAttribute('movie_id'));
            } catch (\Exception $e) {
                return $response->withJson($e->getMessage(), $e->getCode());
            }

            return $response->withJson($result);
        });

        return $slim;
    }
}
