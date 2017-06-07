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
     * @return \stdClass
     */
    public function getOneByMovieId(int $movie_id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM movieratings WHERE movie_id = ?');

        // Throws exception on connection error
        if (!$statement->execute([$movie_id])) {
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
     * Defines create method
     *
     * @param  int $movie_id
     * @param  int $average_rating
     * @param  int $total_ratings
     * @return \stdClass
     */
    public function postNew(int $movie_id, int $average_rating, int $total_ratings)
    {
        // Prepares insert statement
        $statement = $this->connection->prepare('INSERT INTO movieratings (movie_id, average_rating, total_ratings) VALUES (?, ?, ?)');

        // Throws exception on integrity constraint violation
        try {
            $result = $statement->execute([$movie_id, $average_rating, $total_ratings]);
        } catch (\Exception $e) {
            throw new \Exception('MovieRating already exists for Movie ID '.$movie_id, static::HTTP_CONFLICT);
        }

        // Throws exception on connection error
        if (!$result) {
            throw new \Exception('Error creating MovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Returns JSON resource object
        return (object) ['type' => 'movieratings', 'id' => $this->connection->lastInsertId(), 'attributes' => ['average_rating' => $average_rating, 'total_ratings' => $total_ratings], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => $movie_id]]]];
    }

    /**
     * Defines update method
     * @todo alter function signature to allow updating a subset of record fields
     *
     * @param  int $movie_id
     * @param  int $average_rating
     * @param  int $total_ratings
     * @return \stdClass
     */
    public function patchByMovieId(int $movie_id, int $average_rating, int $total_ratings)
    {
        // Prepares update statement. Sets mysql last_insert_id to matching record id for return
        $statement = $this->connection->prepare('UPDATE movieratings SET average_rating = ?, total_ratings = ?, id = LAST_INSERT_ID(id) WHERE movie_id = ?');

        // Throws exception on connection error
        try {
            $result = $statement->execute([$average_rating, $total_ratings, $movie_id]);
        } catch (\Exception $e) {
            throw new \Exception('Error updating MovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Throws exception when update fails
        if (!($id = $this->connection->lastInsertId())) {
            throw new \Exception('No MovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        // Returns JSON resource object
        return (object) ['type' => 'movieratings', 'id' => $id, 'attributes' => ['average_rating' => $average_rating, 'total_ratings' => $total_ratings], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => $movie_id]]]];
    }
}

        // try {
        //     // Fetches current rating data. Execution may stop here on exception
        //     $current = $this->getOneByMovieId($movie_id);

        //     // Calculates new total number of ratings
        //     $new_weight = $current->total_ratings + $rating_weight;

        //     // Calculates new average rating
        //     $new_rating = (($current->average_rating * $current->total_ratings) + ($movie_rating * $rating_weight)) / $new_weight;
        // } catch (\Exception $e) {
        //     // Bubbles up exception from read operation unless no matching record found
        //     if ($e->getCode() !== static::HTTP_BAD_REQUEST) {
        //         throw $e;
        //     }
        // }
