<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

class MoviesModelTest extends \PHPUnit\Framework\TestCase
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
        $this->model = new \RestSample\PdoModels\MoviesModel($this->getConnection()->getConnection());
    }

    // Implements method required by \PHPUnit\DbUnit\TestCaseTrait::setUp()
    public function getDataSet(): \PHPUnit\DbUnit\DataSet\FlatXmlDataSet
    {
        return $this->createFlatXmlDataSet(dirname(__DIR__).'/_files/movies-fixture.xml');
    }

    /**
     * @test
     */
    public function testModelFetchReturnsExistingRecord()
    {
        $expected = (object) ['type' => 'movies', 'id' => '1', 'attributes' => ['name' => 'Avatar']];
        $this->assertEquals($expected, $this->model->getOneById(1));
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->getOneById(9);
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForInvalidId()
    {
        $this->expectException(\TypeError::class);
        $this->model->getOneById('id');
    }

    /**
     * @test
     */
    public function testModelFetchThrowsExceptionForMissingId()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->getOneById();
    }
}
