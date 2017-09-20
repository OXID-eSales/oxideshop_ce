<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:10
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;

use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDao;

class PriceInformationDaoIITest extends AbstractOxArticlesTest
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
    public function testAllRowsForFixtures()
    {

        // For rows in fixture
        $this->assertEquals(1, $this->connection->getRowCount('oxarticles'));
    }

    /**
     * @dataProvider priceGroupInformationTestInput
     */
    public function testFetchGroupPriceInformation($priceGroup, $articleId, $shopId, $expected)
    {

        $result = $this->priceInformationDao->getGroupPrice($priceGroup, $articleId, $shopId);
        $this->assertEquals($expected, $result);
    }

    public function priceGroupInformationTestInput()
    {

        return [
            ['priceGroup' => 'a', 'articleId' => 1, 'shopId' => 1, 'expected' => 10.50],
            ['priceGroup' => 'b', 'articleId' => 1, 'shopId' => 1, 'expected' => 11.60],
            ['priceGroup' => 'c', 'articleId' => 1, 'shopId' => 1, 'expected' => 12.70]
        ];
    }

    /**
     * If the group does not exist, an exception should be thrown
     */
    public function testBulkPriceNonExistingGroup()
    {

        $this->setExpectedException(\Exception::class);

        $this->priceInformationDao->getGroupPrice('d', 1);
    }

    /**
     * If the article is not found, an exception should be thrown
     */
    public function testBulkPriceNonExistingArticle()
    {

        $this->setExpectedException(\Exception::class);

        $this->priceInformationDao->getGroupPrice('a', 2);
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../../Fixtures/OxArticlesGroupPriceTestFixture.xml';
    }

}