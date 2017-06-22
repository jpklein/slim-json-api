<?php declare(strict_types=1);

namespace RestSample;

/**
 * Connects controller to model
 */
trait SlimControllerTrait
{
    static private $db = null;

    /**
     * @param  PDO $db
     */
    public function __construct(\PDO $db)
    {
        self::$db =& $db;
    }

    /**
     * Connects controller to model on demand
     *
     * @return RestSample\PdoModel
     */
    abstract protected function getModel() : PdoModel;
}
