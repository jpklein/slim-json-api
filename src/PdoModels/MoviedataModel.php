<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

// Interface to Moviedata entity
class MoviedataModel extends \RestSample\PdoModel
{
    // Initializes model
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->entity = (object) ['movie_id' => null, 'serialized' => null];
    }

    /**
     * Defines read method
     *
     * @param  int $movie_id
     * @return \stdClass|false
     */
    public function getMovieDataById(int $movie_id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM moviedata WHERE movie_id = :movie_id');

        // Throws exception on connection error
        if (!$statement->execute([':movie_id' => $movie_id]) || !$statement->setFetchMode(\PDO::FETCH_INTO, $this->entity)) {
            throw new \Exception('Error fetching MovieData by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates object
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        // Throws exception when object contains no data
        if (!$result || is_null($result)) {
            throw new \Exception('No MovieData for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        return $result;
    }
}
