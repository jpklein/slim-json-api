<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

use \RestSample\PdoModel as PdoModel;
use \RestSample\PdoModels\UsermovieratingsModel as ModelUnderTest;

class UsermovieratingsModelTest extends \PHPUnit\Framework\TestCase
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
        $this->model = new \RestSample\PdoModels\UsermovieratingsModel($this->getConnection()->getConnection());
    }

    // Implements method required by \PHPUnit\DbUnit\TestCaseTrait::setUp()
    public function getDataSet(): \PHPUnit\DbUnit\DataSet\FlatXmlDataSet
    {
        return $this->createFlatXmlDataSet(dirname(__DIR__).'/_files/usermovieratings-fixture.xml');
    }

    /** Tests UsermovieratingsModel::getOneByPrimaryKeys() **/

    /**
     * @test
     */
    public function testFetchReturnsExistingRecord()
    {
        // Mocks object with provided id, rating, user_id, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '1', '10', '1', '1');

        // Returns object with provided user_id, movie_id, rating
        $actual = $this->model->getOneByPrimaryKeys(1, 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->getOneByPrimaryKeys(1, 9);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForInvalidPrimaryKeys()
    {
        $this->expectException(\TypeError::class);
        $this->model->getOneByPrimaryKeys('1', 1);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingPrimaryKeys()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->getOneByPrimaryKeys(1);
    }

    /** Tests UsermovieratingsModel::postNew() **/

    /**
     * @test
     */
    public function testCreateReturnsNewRecord()
    {
        // Mocks object with provided id, rating, user_id, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '4', '8', '4', '1');

        // Returns object with provided user_id, movie_id, rating
        $actual = $this->model->postNew(4, 1, 8);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testCreateThrowsExceptionForExistingPrimaryKeys()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(\RestSample\PdoModel::HTTP_CONFLICT);
        $this->model->postNew(1, 1, 1);
    }

    /**
     * @test
     */
    public function testCreateThrowsExceptionForInvalidArguments()
    {
        $this->expectException(\TypeError::class);
        $this->model->postNew(4, 1, '8');
    }

    /** Tests UsermovieratingsModel::patchByPrimaryKeys() **/

    /**
     * @test
     */
    public function testUpdateReturnsExistingRecord()
    {
        // Mocks object with provided id, rating, user_id, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '1', '8', '1', '1');

        // Returns object with provided user_id, movie_id, rating
        $expected = $this->model->patchByPrimaryKeys(1, 1, 8);

        $this->assertEquals($expected, $expected);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->patchByPrimaryKeys(9, 1, 8);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForInvalidMovieId()
    {
        $this->expectException(\TypeError::class);
        $this->model->patchByPrimaryKeys('1', 1, 8);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForMissingArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->patchByPrimaryKeys(1, 8);
    }
}
