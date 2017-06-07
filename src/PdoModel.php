<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample;

/**
 * Declares the ORM
 * @todo Uses trait to provide method for displaying error messages in response body
 */
class PdoModel
{
    // PDO connection
    protected $connection;

    // Error codes
    const HTTP_BAD_REQUEST = 400;
    const HTTP_CONFLICT = 409;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    // Initializes database connection
    final public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    // Replaces null values in nested array with subsequent argument values
    public static function getObjectFromTemplate(array $template)
    {
        $stack = func_get_args();
        $template = array_shift($stack);
        array_walk_recursive($template, function (&$item) use (&$stack) {
            if (is_null($item)) {
                $item = array_shift($stack);
            }
        });

        return (object) $template;
    }
}
