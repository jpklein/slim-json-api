<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

class MoviedataModelTest extends \PHPUnit\Framework\TestCase
{
    // Includes DBUnit connection for testing
    use \RestSample\Tests\PdoModelTestTrait {
        \RestSample\Tests\PdoModelTestTrait::setUp as fixureSetUp;
    }

    public function setUp()
    {
        // Calls \PHPUnit\DbUnit\TestCaseTrait::setUp()
        $this->traitSetUp();

        // Injects PDO connection from DBUnit DefaultConnection object
        $this->model = new \RestSample\PdoModels\MoviedataModel($this->getConnection()->getConnection());
    }

    // Implements method required by \PHPUnit\DbUnit\TestCaseTrait::setUp()
    public function getDataSet(): \PHPUnit\DbUnit\DataSet\FlatXmlDataSet
    {
        return $this->createFlatXmlDataSet(dirname(__DIR__).'/_files/moviedata-fixture.xml');
    }

    public function testGetRowCount()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('moviedata'));
    }

    public function testTwo()
    {
        $expected = (object) ['movie_id' => 1, 'serialized' => '{"name":"Jaws"}'];
        $this->assertEquals($expected, $this->model->getMovieDataById(1));
    }
}
