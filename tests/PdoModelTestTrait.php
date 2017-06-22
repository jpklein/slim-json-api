<?php declare(strict_types=1);

namespace RestSample\Tests;

use \PHPUnit\DbUnit\Database\DefaultConnection;

/**
 * Provides DBUnit connection to persistance layer for testing
 */
trait PdoModelTestTrait
{
    use \PHPUnit\DbUnit\TestCaseTrait;

    static private $pdo = null;
    private $connection = null;

    final public function getConnection(): DefaultConnection
    {
        if ($this->connection === null) {
            if (self::$pdo == null) {
                self::$pdo = \RestSample\App::withConfig()->getDbConnection();
            }
            $this->connection = $this->createDefaultDBConnection(self::$pdo);
        }

        return $this->connection;
    }
}
