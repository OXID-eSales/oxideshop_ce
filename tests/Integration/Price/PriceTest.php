<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Price;

use oxDb;

require_once __DIR__. '/BasketConstruct.php';

/**
 * Shop price calculation test
 * Check:
 * - Price
 * - Unit price
 * - Total price
 * - Amount price info
 */
class PriceTest extends BaseTestCase
{
    /** @var array Test case directories. */
    private $testCasesDirectory = "testcases/price";

    /** @var array If specified, runs only these test cases. */
    private $testCases = array();

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->resetDatabase();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Resets db tables, required configs
     */
    protected function resetDatabase()
    {
        $database = oxDb::getDb();
        $database->execute("TRUNCATE oxarticles");
        $database->execute("TRUNCATE oxdiscount");
        $database->execute("TRUNCATE oxobject2discount");
        $database->execute("TRUNCATE oxprice2article");
        $tables = $database->getCol("SHOW TABLES");
        if (in_array('oxfield2shop', $tables)) {
            $database->execute("TRUNCATE oxfield2shop");
        }
        $database->execute("TRUNCATE oxuser");
        $database->execute("TRUNCATE oxobject2group");
        $database->execute("TRUNCATE oxgroups");
    }

    /**
     * Order startup data and expected calculations results
     *
     * @return array
     */
    public function providerPrice()
    {
        $directoriesToScan = array($this->testCasesDirectory . '/community/');
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $directoriesToScan[] = $this->testCasesDirectory . '/enterprise/';
        }
        return $this->getTestCases($directoriesToScan, $this->testCases);
    }

    /**
     * Tests price calculation
     *
     * @dataProvider providerPrice
     *
     * @param array $aTestCase
     */
    public function testPrice($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        // gather data from test case
        $aExpected = $aTestCase['expected'];

        // load calculated basket from provided data
        $oConstruct = new BasketConstruct();
        // create shops
        $iActiveShopId = $oConstruct->createShop($aTestCase['shop']);

        // create user if specified
        $oUser = $oConstruct->createObj($aTestCase['user'], "oxuser", "oxuser");

        // create group and assign
        $oConstruct->createGroup($aTestCase['group']);

        // user login
        if ($oUser) {
            $oUser->load($oUser->getId());
            $oUser->login($aTestCase['user']['oxusername'], '');
        }

        // setup options
        $oConstruct->setOptions($aTestCase['options']);

        // create categories
        $oConstruct->setCategories($aTestCase['categories']);

        // create articles
        $articlesData = $oConstruct->getArticles($aTestCase['articles']);

        // apply discounts
        $oConstruct->setDiscounts($aTestCase['discounts']);

        // set active shop
        if ($iActiveShopId != 1) {
            $oConstruct->setActiveShop($iActiveShopId);
        }

        // iteration through expectations
        foreach ($articlesData as $articleData) {
            $expected = $aExpected[$articleData['id']];
            if (empty($expected)) {
                continue;
            }
            $article = oxNew('oxArticle');
            $article->load($articleData['id']);

            $this->assertEquals($expected['base_price'], $this->getFormatted($article->getBasePrice()), "Base Price of article #{$articleData['id']}");
            $this->assertEquals($expected['price'], $article->getFPrice(), "Price of article #{$articleData['id']}");

            if (isset($expected['rrp_price'])) {
                $this->assertEquals($expected['rrp_price'], $article->getFTPrice(), "RRP price of article #{$articleData['id']}");
            }

            if (isset($expected['unit_price'])) {
                $this->assertEquals($expected['unit_price'], $article->getFUnitPrice(), "Unit Price of article #{$articleData['id']}");
            }

            if (isset($expected['is_range_price'])) {
                $this->assertEquals($expected['is_range_price'], $article->isRangePrice(), "Is range price check of article #{$articleData['id']}");
            }

            if (isset($expected['min_price'])) {
                $this->assertEquals($expected['min_price'], $article->getFMinPrice(), "Min price of article #{$articleData['id']}");
            }

            if (isset($expected['var_min_price'])) {
                $this->assertEquals($expected['var_min_price'], $article->getFVarMinPrice(), "Var min price of article #{$articleData['id']}");
            }

            if (isset($expected['show_rrp'])) {
                $blShowRPP = false;
                if ($article->getTPrice() && $article->getTPrice()->getPrice() > $article->getPrice()->getPrice()) {
                    $blShowRPP = true;
                }
                $this->assertEquals($expected['show_rrp'], $blShowRPP, "RRP price showing of article #{$articleData['id']}");
            }
        }
    }

    /**
     * Get formatted price
     *
     * @param double $dPrice
     *
     * @return double
     */
    protected function getFormatted($dPrice)
    {
        return number_format(round($dPrice, 2), 2, ',', '.');
    }

    /**
     * Truncates specified table
     *
     * @param string $sTable table name
     */
    protected function truncateTable($sTable)
    {
        oxDb::getDb()->execute("TRUNCATE {$sTable}");
    }
}
