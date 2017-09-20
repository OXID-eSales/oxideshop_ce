<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:10
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;

use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDao;
use OxidEsales\EshopCommunity\Internal\DataObject\BulkPriceInfo;

class PriceInformationDaoITest extends AbstractOxArticlesTest
{

    /** @var  PriceInformationDao $priceInformationDao */
    private $priceInformationDao;

    public function setUp()
    {

        parent::setUp();
        $this->priceInformationDao = new PriceInformationDao($this->getDoctrineConnection(),
            $this->contextStub, $this->legacyServiceStub);
    }

    /**
     * Just confirms that the fixture is correctly loaded
     */
    public function testAllRowsForPrice2Articles()
    {

        // For rows in fixture
        $this->assertEquals(3, $this->connection->getRowCount('oxprice2article'));
    }

    /**
     * @dataProvider bulkPriceInformationTestInput
     */
    public function testFetchBulkPriceInformation($amount, $articleId, $shopId, $expected)
    {

        $bulkPriceInfo = $this->priceInformationDao->getBulkPriceInformation($amount, $articleId, $shopId);
        $this->assertEquals($expected, $bulkPriceInfo->calculateBulkPrice(1.0));
    }

    public function bulkPriceInformationTestInput()
    {

        return [
            ['amount' => 11, 'articleId' => 1, 'shopId' => 1, 'expected' => 9.98], // absolute value
            ['amount' => 9, 'articleId' => 1, 'shopId' => 1, 'expected' => 1.0],  // no result
            ['amount' => 20, 'articleId' => 1, 'shopId' => 1, 'expected' => 1.0], // no result
            ['amount' => 11, 'articleId' => 2, 'shopId' => 1, 'expected' => 1.0], // no result
            ['amount' => 11, 'articleId' => 1, 'shopId' => 2, 'expected' => 1.0], // no result
            ['amount' => 21, 'articleId' => 2, 'shopId' => 1, 'expected' => 0.86]  // 14% discount
        ];
    }

    /**
     * If there are conflicting results, an exception should be thrown
     */
    public function testBulkPriceConsistencyHandling()
    {

        $this->setExpectedException(\Exception::class);

        $this->priceInformationDao->getBulkPriceInformation(50, 2);
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../../Fixtures/OxPrice2ArticleTestFixture.xml';
    }

}