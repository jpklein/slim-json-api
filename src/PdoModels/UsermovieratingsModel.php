<?php declare(strict_types=1);

namespace RestSample\PdoModels;

use RestSample\Exceptions\JsonApiException as Exception;

/**
 * Interface to UserMovieRating entity
 *
 * Represents a user's rating of a movie.
 */
class UsermovieratingsModel extends \RestSample\PdoModel
{
    const RESOURCE_TEMPLATE = [
        'type'          => 'usermovieratings',
        'id'            => null,
        'attributes'    => [
            'rating' => null
        ],
        'relationships' => [
            'users'  => [
                'data' => [
                    'type' => 'users',
                    'id'   => null
                ]
            ],
            'movies' => [
                'data' => [
                    'type' => 'movies',
                    'id'   => null
                ]
            ]
        ]
    ];

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
            throw new Exception('Error fetching UserMovieRating by Movie ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates array
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        // Throws exception when array contains no data
        if (!$result || empty(array_filter($result))) {
            throw new Exception('No UserMovieRating for Movie ID '.$movie_id, static::HTTP_NOT_FOUND);
        }

        // Returns JSON resource object
        return self::getObjectFromTemplate(self::RESOURCE_TEMPLATE, $result['id'], $result['rating'], $result['user_id'], $result['movie_id']);
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
            throw new Exception('UserMovieRating already exists for Movie ID '.$movie_id, static::HTTP_CONFLICT);
        }

        // Throws exception on connection error
        if (!$result) {
            throw new Exception('Error creating UserMovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Returns JSON resource object
        return self::getObjectFromTemplate(self::RESOURCE_TEMPLATE, $this->connection->lastInsertId(), (string) $rating, (string) $user_id, (string) $movie_id);
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
            throw new Exception('Error updating UserMovieRating for Movie ID '.$movie_id, static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Throws exception when update fails
        if (!($id = $this->connection->lastInsertId())) {
            throw new Exception('No UserMovieRating for Movie ID '.$movie_id, static::HTTP_NOT_FOUND);
        }

        // Returns JSON resource object
        return self::getObjectFromTemplate(self::RESOURCE_TEMPLATE, (string) $id, (string) $rating, (string) $user_id, (string) $movie_id);
    }
}
