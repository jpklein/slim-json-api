<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\PdoModels;

use RestSample\Exceptions\JsonApiException as Exception;

// Interface to Movie entity
class MoviesModel extends \RestSample\PdoModel
{
    const RESOURCE_TEMPLATE = [
        'type'          => 'movies',
        'id'            => null,
        'attributes'    => null
    ];

    /**
     * Defines read method
     *
     * @param  int $id
     * @return \stdClass|false
     */
    public function getOneById(int $id)
    {
        // Prepares select statement
        $statement = $this->connection->prepare('SELECT * FROM movies WHERE id = ?');

        // Throws exception on connection error
        if (!$statement->execute([$id])) {
            throw new Exception('Error fetching Movie by ID', static::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Populates array
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        // Throws exception when array contains no data
        if (!$result || empty(array_filter($result))) {
            throw new Exception('No Movie for ID '.$id, static::HTTP_NOT_FOUND);
        }

        // Returns JSON resource object
        return self::getObjectFromTemplate(self::RESOURCE_TEMPLATE, $result['id'], json_decode($result['attributes'], true));
    }
}
