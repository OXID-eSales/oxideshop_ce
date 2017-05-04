<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basketconstruct.php';

/**
 * Shop price calculation test
 * Check:
 * - Price
 * - Unit price
 * - Total price
 * - Amount price info
 */
class Integration_Price_PriceTest extends OxidTestCase
{

    /* Test case directory array */
    private $_aTestCaseDirs = array(
        "testcases/price",
    );
    /* Specified test cases (optional) */
    private $_aTestCases = array(//"testCase.php",
    );

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_reset();
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
    protected function _reset()
    {
        $oDb = oxDb::getDb();
        $oConfig = oxRegistry::getConfig();
        $oDb->query("TRUNCATE oxarticles");
        $oDb->query("TRUNCATE oxdiscount");
        $oDb->query("TRUNCATE oxobject2discount");
        $oDb->query("TRUNCATE oxprice2article");
        $oDb->query("TRUNCATE oxuser");
        $oDb->query("TRUNCATE oxobject2group");
        $oDb->query("TRUNCATE oxgroups");
    }


    /**
     * Order startup data and expected calculations results
     */
    public function _dpData()
    {
        return $this->_getTestCases($this->_aTestCaseDirs, $this->_aTestCases);
    }

    /**
     * Tests price calculation
     *
     * @dataProvider _dpData
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
            $oUser->login($aTestCase['user']['oxusername'], '');
        }

        // setup options
        $oConstruct->setOptions($aTestCase['options']);

        // create categories
        $oConstruct->setCategories($aTestCase['categories']);

        // create articles
        $aArts = $oConstruct->getArticles($aTestCase['articles']);

        // apply discounts
        $oConstruct->setDiscounts($aTestCase['discounts']);

        // set active shop
        if ($iActiveShopId != 1) {
            $oConstruct->setActiveShop($iActiveShopId);
        }

        // iteration through expectations
        foreach ($aArts as $aArt) {
            $aExp = $aExpected[$aArt['id']];
            if (empty($aExp)) {
                continue;
            }
            $oArt = new oxArticle();
            $oArt->load($aArt['id']);

            $this->assertEquals($aExp['base_price'], $this->_getFormatted($oArt->getBasePrice()), "Base Price of article #{$aArt['id']}");
            $this->assertEquals($aExp['price'], $oArt->getFPrice(), "Price of article #{$aArt['id']}");

            isset($aExp['rrp_price'])
                ? $this->assertEquals($aExp['rrp_price'], $oArt->getFTPrice(), "RRP price of article #{$aArt['id']}")
                : '';
            isset($aExp['unit_price'])
                ? $this->assertEquals($aExp['unit_price'], $oArt->getFUnitPrice(), "Unit Price of article #{$aArt['id']}")
                : '';
            isset($aExp['is_range_price'])
                ? $this->assertEquals($aExp['is_range_price'], $oArt->isRangePrice(), "Is range price check of article #{$aArt['id']}")
                : '';

            isset($aExp['min_price'])
                ? $this->assertEquals($aExp['min_price'], $oArt->getFMinPrice(), "Min price of article #{$aArt['id']}")
                : '';

            isset($aExp['var_min_price'])
                ? $this->assertEquals($aExp['var_min_price'], $oArt->getFVarMinPrice(), "Var min price of article #{$aArt['id']}")
                : '';

            if (isset($aExp['show_rrp'])) {
                $blShowRPP = false;
                if ($oArt->getTPrice() && $oArt->getTPrice()->getPrice() > $oArt->getPrice()->getPrice()) {
                    $blShowRPP = true;
                }
                $this->assertEquals($aExp['show_rrp'], $blShowRPP, "RRP price showing of article #{$aArt['id']}");
            }
        }
    }

    /**
     * Get formatted price
     *
     * @param double $dPrice
     */
    protected function _getFormatted($dPrice)
    {
        return number_format(round($dPrice, 2), 2, ',', '.');
    }

    /**
     * Getting test cases from specified
     *
     * @param string $sDir       directory name
     * @param array  $aTestCases of specified test cases
     */
    protected function _getTestCases($aDir, $aTestCases = array())
    {
        $aGlobal = array();
        foreach ($aDir as $sDir) {
            $sPath = __DIR__ ."/" . $sDir . "/";
            print("Scanning dir {$sPath}\r\n");
            if (empty($aTestCases)) {
                $aFiles = glob($sPath . "*.php", GLOB_NOSORT);
                if (empty($aFiles)) {
                    $aSubDirs = scandir($sPath);
                    foreach ($aSubDirs as $sSubDir) {
                        $sPath = __DIR__ ."/" . $sDir . "/" . $sSubDir . "/";
                        $aFiles = array_merge($aFiles, glob($sPath . "*.php", GLOB_NOSORT));
                    }
                }
            } else {
                foreach ($aTestCases as $sTestCase) {
                    $aFiles[] = $sPath . $sTestCase;
                }
            }
            print(count($aFiles) . " test files found\r\n");
            foreach ($aFiles as $sFilename) {
                if (!file_exists($sFilename)) {
                    throw new Exception("Test case {$sFilename} does not exist!");
                }
                include($sFilename);
                if ($aData) {
                    $aGlobal["{$sFilename}"] = array($aData);
                }
            }
        }

        return $aGlobal;
    }

    /**
     * Truncates specified table
     *
     * @param string $sTable table name
     */
    protected function _truncateTable($sTable)
    {
        oxDb::getDb()->execute("TRUNCATE {$sTable}");
    }

}