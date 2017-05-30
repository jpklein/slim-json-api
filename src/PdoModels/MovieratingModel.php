<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

// Interface to Movierating entity
class MovieratingModel extends \RestSample\PdoModel
{
    // Initializes model
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->entity = (object) ['movie_id' => null, 'average_rating' => null, 'total_ratings' => null];
    }

    /**
     * Defines read method. Retrieves overall movie rating based on all users' ratings
     *
     * @param  int $movie_id
     * @return \stdClass|false
     */
    public function getMovieRatingById(int $movie_id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM movieratings WHERE movie_id = :movie_id');

        // Throws exception on connection error
        if (!$statement->execute([':movie_id' => $movie_id]) || !$statement->setFetchMode(\PDO::FETCH_INTO, $this->entity)) {
            throw new \Exception('Error fetching MovieRating by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates object
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        // Throws exception when object contains no data
        if (!$result || is_null($result)) {
            throw new \Exception('No MovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        return $result;
    }
}
