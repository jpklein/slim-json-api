<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

namespace RestSample;

/**
 * Declares the ORM
 *
 * @todo Provides method for displaying error messages in response body
 */
class PdoModel
{
    // PDO connection
    private $connection;

    // Entity description
    private $entity;

    // Error codes
    const HTTP_BAD_REQUEST = 400;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
}
