<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

// Restricts usage to command-line
if (PHP_SAPI !== 'cli') {
    die();
}

// Ignores script filename
unset($argv[0]);

// Parses command-line arguments
foreach ($argv as $arg) {
    $sql = '';

    switch ($arg) {
        case 'install':
            // Creates table for movie data
            $sql .= 'CREATE TABLE movies (id INT NOT NULL AUTO_INCREMENT, attributes BLOB, PRIMARY KEY (id));';
            // Creates table for movieratings
            $sql .= 'CREATE TABLE movieratings (id INT NOT NULL AUTO_INCREMENT, movie_id INT NOT NULL, average_rating INT NOT NULL, total_ratings INT NOT NULL, PRIMARY KEY (id), UNIQUE (movie_id));';
            // Creates table for usermovieratings
            $sql .= 'CREATE TABLE usermovieratings (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, movie_id INT NOT NULL, rating INT NOT NULL, PRIMARY KEY (id), UNIQUE (user_id, movie_id));';
            break;

        case 'stage':
            // Prepares PDO connection
            require_once 'src/App.php';
            $connection = $connection ?? \RestSample\App::withConfig()->getDbConnection();
            // Generates dummy movie records
            $statement = $connection->prepare('INSERT INTO movies (attributes) VALUES (?);INSERT INTO movies (attributes) VALUES (?);INSERT INTO movies (attributes) VALUES (?);');
            $statement->execute([json_encode(['name'=>'Jaws']), json_encode(['name'=>'The Ten Commandments']), json_encode(['name'=>'Titanic'])]);
            // Generates dummy movieratings records
            $statement = $connection->prepare('INSERT INTO movieratings (movie_id, average_rating, total_ratings) VALUES (?, ?, ?);');
            $statement->execute([1, 4, 3]);
            // Generates dummy usermovieratings records
            $statement = $connection->prepare('INSERT INTO usermovieratings (user_id, movie_id, rating) VALUES (?, ?, ?);INSERT INTO usermovieratings (user_id, movie_id, rating) VALUES (?, ?, ?);INSERT INTO usermovieratings (user_id, movie_id, rating) VALUES (?, ?, ?);');
            $statement->execute([1, 1, 10, 2, 1, 1, 3, 1, 1]);
            // Continues with the next iteration of foreach
            continue(2);

        case 'unstage':
            // Truncates table for movies
            $sql .= 'TRUNCATE TABLE movies;';
            // Truncates table for movieratings
            $sql .= 'TRUNCATE TABLE movieratings;';
            // Truncates table for usermovieratings
            $sql .= 'TRUNCATE TABLE usermovieratings;';
            break;

        case 'uninstall':
            // Includes settings definition
            require_once 'src/App.php';
            $connection = $connection ?? \RestSample\App::withConfig()->getDbConnection();
            // Drops app database
            $connection->exec('DROP DATABASE IF EXISTS '.APP_CONFIG['db']['dbname']);
            unset($connection);
            continue(2);

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
