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

    /**
     * Defines create/update method. Submits movie rating per user
     * @todo replace try/catch with sql variable manipulation to handle calculations
     *
     * @param  int $movie_id
     * @param  int $movie_rating
     * @param  int $rating_weight
     * @return \stdClass|false
     */
    public function setMovieRatingById(int $movie_id, int $movie_rating, int $rating_weight = 1)
    {
        // Prepares query parameters
        $bindings = [':movie_id' => $movie_id, ':average_rating' => $movie_rating, ':total_ratings' => $rating_weight];

        try {
            // Fetches current rating data. Execution may stop here on exception
            $current = $this->getMovieRatingById($movie_id);

            // Calculates new total number of ratings
            $new_weight = $current->total_ratings + $rating_weight;

            // Calculates new average rating
            $new_rating = (($current->average_rating * $current->total_ratings) + ($movie_rating * $rating_weight)) / $new_weight;

            // Updates query parameters
            $bindings[':average_rating'] = $new_rating;
            $bindings[':total_ratings'] = $new_weight;
        } catch (\Exception $e) {
            // Bubbles up exception from read operation unless no matching record found
            if ($e->code !== static::HTTP_BAD_REQUEST) {
                throw $e;
            }
        }

        // Performs update if insert would cause a duplicate primary key value
        $sql = 'INSERT INTO movieratings (movie_id, average_rating, total_ratings) VALUES (:movie_id, :average_rating, :total_ratings) ON DUPLICATE KEY UPDATE average_rating=:average_rating, total_ratings=:total_ratings';
        $statement = $this->connection->prepare($sql);
        $result = $statement->execute($bindings);

        // Throws exception on connection error
        if (!$result) {
            throw new \Exception('Error saving MovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;
    }
}