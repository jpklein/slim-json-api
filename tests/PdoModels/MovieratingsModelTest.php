<?php declare(strict_types=1);

namespace RestSample\Tests\PdoModels;

use \RestSample\PdoModel as PdoModel;
use \RestSample\PdoModels\MovieratingsModel as ModelUnderTest;

/**
 * Test suite for MovieRating ORM
 */
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
        // Mocks object with provided id, average_rating, total_ratings, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '1', '4', '3', '1');

        // Returns object with provided movie_id
        $actual = $this->model->getOneByMovieId(1);

        $this->assertEquals($expected, $actual);
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
        $this->model->getOneByMovieId('1');
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
        // Mocks object with provided id, average_rating, total_ratings, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '2', '5', '1', '2');

        // Returns object with provided movie_id, average_rating, total_ratings
        $actual = $this->model->postNew(2, 5, 1);

        $this->assertEquals($expected, $actual);
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
        // Mocks object with provided id, average_rating, total_ratings, movie_id
        $expected = PdoModel::getObjectFromTemplate(ModelUnderTest::RESOURCE_TEMPLATE, '1', '5', '4', '1');

        // Returns object with provided movie_id, average_rating, total_ratings
        $actual = $this->model->patchByMovieId(1, 5, 4);

        $this->assertEquals($expected, $actual);
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
        $this->model->patchByMovieId('1', 5, 4);
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
