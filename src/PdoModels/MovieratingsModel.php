<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

// Interface to Movierating entity
class MovieratingsModel extends \RestSample\PdoModel
{
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
        if (!$statement->execute([':movie_id' => $movie_id])) {
            throw new \Exception('Error fetching MovieRating by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates array
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        // Throws exception when array contains no data
        if (!$result || empty(array_filter($result))) {
            throw new \Exception('No MovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        // Returns JSON resource object
        return (object) ['type' => 'movieratings', 'id' => $result['id'], 'attributes' => ['average_rating' => $result['average_rating'], 'total_ratings' => $result['total_ratings']], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => $result['movie_id']]]]];
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
        try {
            // Fetches current rating data. Execution may stop here on exception
            $current = $this->getMovieRatingById($movie_id);

            // Calculates new total number of ratings
            $new_weight = $current->total_ratings + $rating_weight;

            // Calculates new average rating
            $new_rating = (($current->average_rating * $current->total_ratings) + ($movie_rating * $rating_weight)) / $new_weight;
        } catch (\Exception $e) {
            // Bubbles up exception from read operation unless no matching record found
            if ($e->getCode() !== static::HTTP_BAD_REQUEST) {
                throw $e;
            }
        }

        // Performs update if insert would cause a duplicate primary key value
        $statement = $this->connection->prepare('INSERT INTO movieratings (movie_id, average_rating, total_ratings) VALUES (:movie_id, :average_rating, :total_ratings) ON DUPLICATE KEY UPDATE average_rating=:average_rating, total_ratings=:total_ratings');

        // Throws exception on connection error
        if (!$statement->execute([':movie_id' => $movie_id, ':average_rating' => $new_rating, ':total_ratings' => $new_weight])) {
            throw new \Exception('Error saving MovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates return object
        return (object) ['movie_id' => $movie_id, 'average_rating' => $new_rating, 'total_ratings' => $new_weight];
    }
}
