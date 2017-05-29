<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    die();
}

unset($argv[0]);

foreach ($argv as $arg) {
    $sql = '';

    switch ($arg) {
        case 'install':
            // Creates table for moviedata
            $sql .= 'CREATE TABLE moviedata (movie_id INT NOT NULL, serialized BLOB, PRIMARY KEY(movie_id));';
            // Creates table for movieratings
            $sql .= 'CREATE TABLE movieratings (movie_id INT NOT NULL, average_rating INT NOT NULL, total_ratings INT NOT NULL, PRIMARY KEY(movie_id));';
            // Creates table for usermovieratings
            $sql .= 'CREATE TABLE usermovieratings (user_id INT NOT NULL, movie_id INT NOT NULL, rating INT NOT NULL, PRIMARY KEY(user_id, movie_id));';
            break;

        case 'stage':
            // @todo Populates tables with dummy data
            $sql .= '';
            break;

        case 'unstage':
            // Truncates table for moviedata
            $sql .= 'TRUNCATE TABLE moviedata;';
            // Truncates table for movieratings
            $sql .= 'TRUNCATE TABLE movieratings;';
            // Truncates table for usermovieratings
            $sql .= 'TRUNCATE TABLE usermovieratings;';
            break;

        case 'uninstall':
            // Includes settings definition
            require_once 'src/App.php';
            // Drops app database
            $sql .= 'DROP DATABASE IF EXISTS '.APP_CONFIG['db']['dbname'];
            break;

        default:
            // Displays available commands
            echo 'Valid commands are "install", "stage", "unstage", and "uninstall"'.PHP_EOL;
            exit(1);
    }

    // Creates new database if it doesn't exist
    require_once 'src/App.php';
    $connection = $connection ?? \RestSample\App::withConfig()->getDbConnection();
    // Executes sql commands
    $connection->exec($sql);
}
