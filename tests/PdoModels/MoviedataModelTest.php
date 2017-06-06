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
        \RestSample\Tests\PdoModelTestTrait::setUp as traitSetUp;
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

    /**
     * @test
     */
    public function testModelFetchReturnsExistingRecord()
    {
        $expected = (object) ['type' => 'movies', 'id' => '1', 'attributes' => ['name' => 'Avatar']];
        $this->assertEquals($expected, $this->model->getMovieDataById(1));
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->getMovieDataById(9);
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForInvalidId()
    {
        $this->expectException(\TypeError::class);
        $this->model->getMovieDataById('id');
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForMissingId()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->getMovieDataById();
    }
}
