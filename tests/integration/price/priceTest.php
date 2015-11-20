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
 * @copyright (C) OXID eSales AG 2003-2016
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

    /** @var array Test case directories. */
    private $testCaseDirectories = array(
        "testcases/price",
    );

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
        $database->query("TRUNCATE oxarticles");
        $database->query("TRUNCATE oxdiscount");
        $database->query("TRUNCATE oxobject2discount");
        $database->query("TRUNCATE oxprice2article");
        $tables = $database->getCol("SHOW TABLES");
        if (in_array('oxfield2shop', $tables)) {
            $database->query("TRUNCATE oxfield2shop");
        }
        $database->query("TRUNCATE oxuser");
        $database->query("TRUNCATE oxobject2group");
        $database->query("TRUNCATE oxgroups");
    }

    /**
     * Order startup data and expected calculations results
     *
     * @return array
     */
    public function providerPrice()
    {
        return $this->getTestCases($this->testCaseDirectories, $this->testCases);
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
        $oConstruct = oxNew('BasketConstruct');
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
            $oArt = oxNew('oxArticle');
            $oArt->load($aArt['id']);

            $this->assertEquals($aExp['base_price'], $this->getFormatted($oArt->getBasePrice()), "Base Price of article #{$aArt['id']}");
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
     *
     * @return double
     */
    protected function getFormatted($dPrice)
    {
        return number_format(round($dPrice, 2), 2, ',', '.');
    }

    /**
     * Getting test cases from specified
     *
     * @param array $directoriesToScan directory name
     * @param array $testCases         of specified test cases
     *
     * @return array
     */
    protected function getTestCases($directoriesToScan, $testCases = array())
    {
        $testCaseFiles = array();
        foreach ($directoriesToScan as $directory) {
            $basePath = __DIR__ . "/$directory/";
            $files = empty($testCases) ? $this->collectFilesFromPath($basePath) : $this->getTestCasesFiles($testCases, $basePath);
            foreach ($files as $file) {
                $aData = array();
                include $file;
                if ($aData) {
                    $testCaseFiles["{$file}"] = array($aData);
                }
            }
        }

        return $testCaseFiles;
    }

    /**
     * @param string $path
     * @param string $collector
     *
     * @return array
     */
    protected function collectFilesFromPath($path, $collector = "*.php")
    {
        $files = glob($path . $collector, GLOB_NOSORT);
        $directories = glob($path.'*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $files = array_merge($files, $this->collectFilesFromPath($directory));
        }

        return $files;
    }

    /**
     * @param array  $testCases
     * @param string $basePath
     *
     * @return array
     */
    protected function getTestCasesFiles($testCases, $basePath)
    {
        $files = array();
        foreach ($testCases as $sTestCase) {
            $file = $basePath . $sTestCase;
            if (file_exists($file)) {
                $files[] = $file;
            }
        }
        return $files;
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