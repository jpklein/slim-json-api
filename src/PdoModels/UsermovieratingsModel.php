<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

// Interface to Usermovierating entity. Represents a user's rating of a movie
class UsermovieratingsModel extends \RestSample\PdoModel
{
    /**
     * Defines read method
     * note: user_id should not be mentioned in exception messages
     *
     * @param  int $user_id
     * @param  int $movie_id
     * @return \stdClass
     */
    public function getOneByPrimaryKeys(int $user_id, int $movie_id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM usermovieratings WHERE user_id = ? AND movie_id = ?');

        // Throws exception on connection error
        if (!$statement->execute([$user_id, $movie_id])) {
            throw new \Exception('Error fetching UserMovieRating by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates array
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        // Throws exception when array contains no data
        if (!$result || empty(array_filter($result))) {
            throw new \Exception('No UserMovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        // Returns JSON resource object
        return (object) ['type' => 'usermovieratings', 'id' => $result['id'], 'attributes' => ['rating' => $result['rating']], 'relationships' => ['users' => ['data' => ['type' => 'users', 'id' => $result['user_id']]]], 'movies' => ['data' => ['type' => 'movies', 'id' => $result['movie_id']]]];
    }

    /**
     * Defines create method
     *
     * @param  int $user_id
     * @param  int $movie_id
     * @param  int $rating
     * @return \stdClass
     */
    public function postNew(int $user_id, int $movie_id, int $rating)
    {
        // Prepares insert statement
        $statement = $this->connection->prepare('INSERT INTO usermovieratings (user_id, movie_id, rating) VALUES (?, ?, ?)');

        // Throws exception on integrity constraint violation
        try {
            $result = $statement->execute([$user_id, $movie_id, $rating]);
        } catch (\Exception $e) {
            throw new \Exception('UserMovieRating already exists for Movie ID '.$movie_id, static::HTTP_CONFLICT);
        }

        // Throws exception on connection error
        if (!$result) {
            throw new \Exception('Error creating UserMovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Returns JSON resource object
        return (object) ['type' => 'usermovieratings', 'id' => $this->connection->lastInsertId(), 'attributes' => ['rating' => $rating], 'relationships' => ['users' => ['data' => ['type' => 'users', 'id' => $user_id]]], 'movies' => ['data' => ['type' => 'movies', 'id' => $movie_id]]];
    }

    /**
     * Defines update method
     * @todo alter function signature to allow updating a subset of record fields
     *
     * @param  int $user_id
     * @param  int $movie_id
     * @param  int $rating
     * @return \stdClass
     */
    public function patchByPrimaryKeys(int $user_id, int $movie_id, int $rating)
    {
        // Prepares update statement. Sets mysql last_insert_id to matching record id for return
        $statement = $this->connection->prepare('UPDATE usermovieratings SET rating = ?, id = LAST_INSERT_ID(id) WHERE user_id = ? AND movie_id = ?');

        // Throws exception on connection error
        try {
            $result = $statement->execute([$rating, $user_id, $movie_id]);
        } catch (\Exception $e) {
            throw new \Exception('Error updating UserMovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Throws exception when update fails
        if (!($id = $this->connection->lastInsertId())) {
            throw new \Exception('No UserMovieRating for Movie ID '.$movie_id, static::HTTP_BAD_REQUEST);
        }

        // Returns JSON resource object
        return (object) ['type' => 'usermovieratings', 'id' => $id, 'attributes' => ['rating' => $rating], 'relationships' => ['users' => ['data' => ['type' => 'users', 'id' => $user_id]]], 'movies' => ['data' => ['type' => 'movies', 'id' => $movie_id]]];
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
