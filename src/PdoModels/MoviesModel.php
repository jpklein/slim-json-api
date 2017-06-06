<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

// Interface to Movie entity
class MoviesModel extends \RestSample\PdoModel
{
    /**
     * Defines read method
     *
     * @param  int $id
     * @return \stdClass|false
     */
    public function getOneById(int $id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM movies WHERE id = :id');

        // Throws exception on connection error
        if (!$statement->execute([':id' => $id])) {
            throw new \Exception('Error fetching Movie by ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates array
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        // Throws exception when array contains no data
        if (!$result || empty(array_filter($result))) {
            throw new \Exception('No Movie for ID '.$id, static::HTTP_BAD_REQUEST);
        }

        // Returns JSON resource object
        return (object) ['type' => 'movies', 'id' => $result['id'], 'attributes' => json_decode($result['attributes'], true)];
    }
}
