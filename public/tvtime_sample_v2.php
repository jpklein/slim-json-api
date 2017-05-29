<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.2
 */

// creates application
$app = new \Slim\App(['settings' => APP_CONFIG]);

// creates dependency injection container
$container = $app->getContainer();


/**
 * Persistance layer
 */

// establishes connection to mysql db
$container['db'] = function ($config)
{
    $db = $config['settings']['db'];
    $pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};


/**
 * Entity definition
 */

// declares the orm
class PdoMapper
{
    // pdo connection
    private $connection;

    // entity description
    private $entity;

    // exception codes
    const HTTP_BAD_REQUEST = 400;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
}

// interface to moviedata entity
class MoviedataMapper extends PdoMapper
{
    // initializes mapper
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->entity = (object)['movie_id' => null, 'serialized' => null];
    }

    // defines read method
    public getMovieDataById($movie_id)
    {
        // prepares select statement
        $sql = 'SELECT * FROM moviedata WHERE movie_id = :movie_id';
        $statement = $this->connection->prepare($sql);

        // throws exception on connection error
        if (!$statement->execute([':movie_id' => $movie_id]) || !$statement->setFetchMode(PDO::FETCH_INTO, $this->entity)) {
            throw new Exception('Error fetching MovieData by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // populates object
        $result = $statement->fetch();

        // throws exception when object contains no data
        if (is_null($result)) {
            throw new Exception('No MovieData for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        return $result;
    }
}

// interface to actormovies entity
class ActormoviesMapper extends PdoMapper
{
    // initializes mapper
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->entity = (object) ['actor_id' => NULL, 'serialized' => NULL];
    }

    // defines read method
    public getActorMoviesById($actor_id)
    {
        // prepares select statement
        $sql = 'SELECT * FROM actormovies WHERE actor_id = :actor_id';
        $statement = $this->connection->prepare($sql);

        // throws exception on connection error
        if (!$statement->execute([':actor_id' => $actor_id]) || !$statement->setFetchMode(PDO::FETCH_INTO, $this->entity)) {
            throw new Exception('Error fetching ActorMovies by Actor ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // populates object
        $result = $statement->fetch();

        // throws exception when object contains no data
        if (is_null($result)) {
            throw new Exception('No ActorMovies for Actor ID '.$actor_id, static::HTTP_BAD_REQUEST);
        }

        return $result;
    }
}

// interface to movierating entity
class MovieratingMapper extends PdoMapper
{
    // initializes mapper
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->entity = (object) ['movie_id' => NULL, 'average_rating' => NULL, 'total_ratings' => NULL];
    }

    // defines read method. retrieve overall movie rating based on all users' ratings
    public getMovieRatingById($movie_id)
    {
        // prepares select statement
        $sql = 'SELECT * FROM movieratings WHERE movie_id = :movie_id';
        $statement = $this->connection->prepare($sql);

        // throws exception on connection error
        if (!$statement->execute([':movie_id' => $movie_id]) || !$statement->setFetchMode(PDO::FETCH_INTO, $this->entity)) {
            throw new Exception('Error fetching MovieRating by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // populates object
        $result = $statement->fetch();

        // throws exception when object contains no data
        if (is_null($result)) {
            throw new Exception('No MovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        return $result;
    }

    // defines update method. submit movie rating per user
    public setMovieRatingById($movie_id, $movie_rating, $rating_weight = 1)
    {
        // prepares query parameters
        $bindings = [':movie_id' => $movie_id, ':average_rating' => $movie_rating, ':total_ratings' => $rating_weight];

        try {
            // fetches current rating data. execution may stop here on exception
            // @todo replace this call with sql containing variable assignment\math statements?
            $current = $this->getMovieRatingById($movie_id);

            // calculates new total number of ratings
            $new_weight = $current->total_ratings + $rating_weight;

            // calculates new average rating
            $new_rating = ( ($current->average_rating * $current->total_ratings) + ($movie_rating * $rating_weight) ) / $new_weight;

            // updates query parameters
            $bindings[':average_rating'] = $new_rating;
            $bindings[':total_ratings'] = $new_weight;
        }
        catch (Exception $e) {
            // bubbles up exception from read operation unless no matching record found
            if ($e->code !== static::HTTP_BAD_REQUEST) {
                throw $e;
            }
        }

        // performs update if insert would cause a duplicate primary key value
        $sql = 'INSERT INTO movieratings (movie_id, average_rating, total_ratings) VALUES (:movie_id, :average_rating, :total_ratings)
                ON DUPLICATE KEY UPDATE average_rating=:average_rating, total_ratings=:total_ratings';
        $statement = $this->connection->prepare($sql);
        $result = $statement->execute($bindings);

        // throws exception on connection error
        if (!$result) {
            throw new Exception('Error saving MovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;
    }
}


/**
 * API endpoints
 */

// includes slim framework psr-7 objects
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// retrieves movie data given a unique ID
$app->get('/moviedata/{movie_id}', function (Request $request, Response $response) {
    try {
        $mapper = new MoviedataMapper($this->db);
        $result = $mapper->getMovieDataById($request->getAttribute('movie_id'));
    }
    catch (Exception $e) {
        return $response->withJson($e->message, $e->code);
    }

    // formats output
    $result->data = unserialize($result->serialized);
    unset($result->serialized);

    return $response->withJson($result);
});

// retrieves what movies an actor is in
$app->get('/actormovies/{actor_id}', function (Request $request, Response $response)
{
    try {
        $mapper = new ActormoviesMapper($this->db);
        $result = $mapper->getActorMoviesById($request->getAttribute('actor_id'));
    }
    catch (Exception $e) {
        return $response->withJson($e->message, $e->code);
    }

    // formats output
    $result->movies = unserialize($result->serialized);
    unset($result->serialized);

    return $response->withJson($result);
});

// retrieves overall movie rating based on all users' ratings
$app->get('/movierating/{movie_id}', function (Request $request, Response $response)
{
    try {
        $mapper = new MovieratingMapper($this->db);
        $result = $mapper->getMovieRatingById($request->getAttribute('movie_id'));
    }
    catch (Exception $e) {
        return $response->withJson($e->message, $e->code);
    }

    return $response->withJson($result);
});

// accepts movie rating per user
// @todo add authentication middleware
// @todo update usermovieratings table to persist individual ratings
$app->post('/movierating/{movie_id}', function (Request $request, Response $response)
{
    // santizes inputs
    $data = $request->getParsedBody();
    $data['movie_rating'] = filter_var($data['movie_rating'], FILTER_VALIDATE_INT);

    try {
        $mapper = new MovieratingMapper($this->db);
        $result = $mapper->setMovieRatingById($request->getAttribute('movie_id'), $data['movie_rating']);
    }
    catch (Exception $e) {
        return $response->withJson($e->message, $e->code);
    }

    return $response->withJson($result);
});

// bootstraps app
$app->run();
