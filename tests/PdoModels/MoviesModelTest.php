<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

use \RestSample\PdoModel as PdoModel;
use \RestSample\PdoModels\MoviesModel as ModelUnderTest;

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

    /** Tests MoviesModel::getOneById() **/

    /**
     * @test
     */
    public function testFetchReturnsExistingRecord()
    {
        // Mocks object with provided id, attributes
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '1', ['name' => 'Avatar']);

        // Returns object with provided id
        $actual = $this->model->getOneById(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->getOneById(9);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForInvalidId()
    {
        $this->expectException(\TypeError::class);
        $this->model->getOneById('1');
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingId()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->getOneById();
    }
}
