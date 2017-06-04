<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests;

trait PdoModelTestTrait
{
    use \PHPUnit\DbUnit\TestCaseTrait;

    static private $pdo = null;
    private $connection = null;

    final public function getConnection(): \PHPUnit\DbUnit\Database\DefaultConnection
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
