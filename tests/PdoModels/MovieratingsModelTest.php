<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

class MovieratingsModelTest extends \PHPUnit\Framework\TestCase
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
        $this->model = new \RestSample\PdoModels\MovieratingsModel($this->getConnection()->getConnection());
    }

    // Implements method required by \PHPUnit\DbUnit\TestCaseTrait::setUp()
    public function getDataSet(): \PHPUnit\DbUnit\DataSet\FlatXmlDataSet
    {
        return $this->createFlatXmlDataSet(dirname(__DIR__).'/_files/movieratings-fixture.xml');
    }

    /** Tests MovieratingsModel::getOneByMovieId() **/

    /**
     * @test
     */
    public function testFetchReturnsExistingRecord()
    {
        $expected = (object) ['type' => 'movieratings', 'id' => '1', 'attributes' => ['average_rating' => '4', 'total_ratings' => '3'], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => '1']]]];
        $this->assertEquals($expected, $this->model->getOneByMovieId(1));
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->getOneByMovieId(9);
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForInvalidMovieId()
    {
        $this->expectException(\TypeError::class);
        $this->model->getOneByMovieId('9');
    }

    /**
     * @test
     */
    public function testFetchThrowsExceptionForMissingMovieId()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->getOneByMovieId();
    }

    /** Tests MovieratingsModel::postNew() **/

    /**
     * @test
     */
    public function testCreateReturnsNewRecord()
    {
        $expected = (object) ['type' => 'movieratings', 'id' => '2', 'attributes' => ['average_rating' => '5', 'total_ratings' => '1'], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => '2']]]];
        $this->assertEquals($expected, $this->model->postNew(2, 5, 1));
    }

    /**
     * @test
     */
    public function testCreateThrowsExceptionForExistingMovieId()
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
        $this->model->postNew(1, 1, '1');
    }

    /** Tests MovieratingsModel::patchByMovieId() **/

    /**
     * @test
     */
    public function testUpdateReturnsExistingRecord()
    {
        $expected = (object) ['type' => 'movieratings', 'id' => '1', 'attributes' => ['average_rating' => '5', 'total_ratings' => '4'], 'relationships' => ['movies' => ['data' => ['type' => 'movies', 'id' => '1']]]];
        $this->assertEquals($expected, $this->model->patchByMovieId(1, 5, 4));
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForMissingRecord()
    {
        $this->expectException(\Exception::class);
        $this->model->patchByMovieId(9, 5, 4);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForInvalidMovieId()
    {
        $this->expectException(\TypeError::class);
        $this->model->patchByMovieId('9', 5, 4);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionForMissingArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->model->patchByMovieId(5, 4);
    }
}
