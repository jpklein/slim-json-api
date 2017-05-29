<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

namespace RestSample;

class AppFactory
{
    private static $app;

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

    public static function getApp(): AppFactory
    {
        if (is_null(self::$app)) {
            self::$app = new self();
        }
        return self::$app;
    }

    public function createDbConnection(): \PDO
    {
        $dsn = 'mysql:host='.APP_CONFIG['db']['host'];
        $pdo = new \PDO($dsn, APP_CONFIG['db']['user'], APP_CONFIG['db']['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec(strtr('CREATE DATABASE ?;USE ?;', ['?' => APP_CONFIG['db']['dbname']]));
        return $pdo;
    }

    public function getDbConnection(): \PDO
    {
        $dsn = 'mysql:host='.APP_CONFIG['db']['host'].';dbname='.APP_CONFIG['db']['dbname'];
        $pdo = new \PDO($dsn, APP_CONFIG['db']['user'], APP_CONFIG['db']['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
