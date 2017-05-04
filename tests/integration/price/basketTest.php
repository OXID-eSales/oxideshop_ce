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
 * Basket price calculation test
 * Check:
 * - Article unit & total price
 * - Discount amounts
 * - Vat amounts
 * - Additional fees (wrapping, payment, delivery)
 * - Vouchers
 * - Totals (grand, netto, brutto)
 */
class Integration_Price_BasketTest extends OxidTestCase
{

    /* Test case directory array */
    private $_aTestCaseDirs = array(
        "testcases/basket",
        //"testcases/databomb"
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
        $this->addTableForCleanup('oxobject2category');
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
        $oDb->query("TRUNCATE oxcategories");
        $oDb->query("TRUNCATE oxdiscount");
        $oDb->query("TRUNCATE oxobject2discount");
        $oDb->query("TRUNCATE oxwrapping");
        $oDb->query("TRUNCATE oxdelivery");
        $oDb->query("TRUNCATE oxdel2delset");
        $oDb->query("TRUNCATE oxobject2payment");
        $oDb->query("TRUNCATE oxvouchers");
        $oDb->query("TRUNCATE oxvoucherseries");
        $oDb->query("TRUNCATE oxobject2delivery");
        $oDb->query("TRUNCATE oxobject2category");
        $oDb->query("TRUNCATE oxdeliveryset");
        $oDb->query("TRUNCATE oxuser");
        $oDb->query("TRUNCATE oxprice2article");
        $oConfig->setConfigParam("blShowVATForDelivery", true);
        $oConfig->setConfigParam("blShowVATForPayCharge", true);
    }

    /**
     * Tests special basket calculations
     *
     * @dataProvider _dpData
     */
    public function testBasketCalculation($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }
        // gathering data arrays
        $aExpected = $aTestCase['expected'];

        //if not finished testing data skip test
        if (empty($aExpected)) {
            $this->markTestSkipped("skipping test case due invalid data provided");
        }

        // load calculated basket from provided data
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        // check basket item list
        $aExpArts = $aExpected['articles'];
        $aBasketItemList = $oBasket->getContents();

        $this->assertEquals(count($aExpArts), count($aBasketItemList), "Expected basket articles amount doesn't match actual");

        if ($aBasketItemList) {
            foreach ($aBasketItemList as $iKey => $oBasketItem) {
                $iArtId = $oBasketItem->getArticle()->getID();
                $this->assertEquals($aExpArts[$iArtId][0], $oBasketItem->getFUnitPrice(), "Unit price of article id {$iArtId}");
                $this->assertEquals($aExpArts[$iArtId][1], $oBasketItem->getFTotalPrice(), "Total price of article id {$iArtId}");
            }
        }

        // Total discounts
        $aExpDisc = $aExpected['totals']['discounts'];
        $aProductDiscounts = $oBasket->getDiscounts();
        $this->assertEquals(count($aExpDisc), count($aProductDiscounts), "Expected basket discount amount doesn't match actual");
        if (!empty($aExpDisc)) {
            foreach ($aProductDiscounts as $oDiscount) {
                $this->assertEquals($aExpDisc[$oDiscount->sOXID], $oDiscount->fDiscount, "Total discount of {$oDiscount->sOXID}");
            }
        }

        // Total vats
        $aExpVats = $aExpected['totals']['vats'];
        $aProductVats = $oBasket->getProductVats();
        $this->assertEquals(count($aExpVats), count($aProductVats), "Expected basket different vat amount doesn't match actual");
        if (!empty($aExpVats)) {
            foreach ($aProductVats as $sPercent => $sSum) {
                $this->assertEquals($aExpVats[$sPercent], $sSum, "Total Vat of {$sPercent}%");
            }
        }

        // Wrapping costs
        $aExpWraps = $aExpected['totals']['wrapping'];
        if (!empty($aExpWraps)) {
            $this->assertEquals(
                $aExpWraps['brutto'],
                $oBasket->getFWrappingCosts(),
                "Total wrappings brutto price"
            );
            $this->assertEquals(
                $aExpWraps['netto'],
                $oBasket->getWrappCostNet(),
                "Total wrappings netto price"
            );
            $this->assertEquals(
                $aExpWraps['vat'],
                $oBasket->getWrappCostVat(),
                "Total wrappings vat price"
            );
        }

        // Giftcard costs 
        $aExpCards = $aExpected['totals']['giftcard'];
        if (!empty($aExpCards)) {
            $this->assertEquals(
                $aExpCards['brutto'],
                $oBasket->getFGiftCardCosts(),
                "Total giftcard brutto price"
            );
            $this->assertEquals(
                $aExpCards['netto'],
                $oBasket->getGiftCardCostNet(),
                "Total giftcard netto price"
            );
            $this->assertEquals(
                $aExpCards['vat'],
                $oBasket->getGiftCardCostVat(),
                "Total giftcard vat price"
            );
        }

        // Delivery costs
        $aExpDel = $aExpected['totals']['delivery'];
        if (!empty($aExpDel)) {
            $this->assertEquals(
                $aExpDel['brutto'],
                number_format(round($oBasket->getDeliveryCosts(), 2), 2, ',', '.'),
                "Delivery total brutto price"
            );
            $this->assertEquals(
                $aExpDel['netto'],
                $oBasket->getDelCostNet(),
                "Delivery total netto price"
            );
            $this->assertEquals(
                $aExpDel['vat'],
                $oBasket->getDelCostVat(),
                "Delivery total vat price"
            );
        }

        // Payment costs 
        $aExpPay = $aExpected['totals']['payment'];
        if (!empty($aExpPay)) {
            $this->assertEquals(
                $aExpPay['brutto'],
                number_format(round($oBasket->getPaymentCosts(), 2), 2, ',', '.'),
                "Payment total brutto price"
            );
            $this->assertEquals(
                $aExpPay['netto'],
                $oBasket->getPayCostNet(),
                "Payment total netto price"
            );
            $this->assertEquals(
                $aExpPay['vat'],
                $oBasket->getPayCostVat(),
                "Payment total vat price"
            );
        }

        // Trusted shop products costs
        $aExpTS = $aExpected['totals']['trustedshop'];
        if (!empty($aExpTS)) {
            $this->assertEquals(
                $aExpTS['brutto'],
                number_format(round($oBasket->getTsProtectionCosts(), 2), 2, ',', '.'),
                "Trusted shop total brutto price"
            );
            $this->assertEquals(
                $aExpTS['netto'],
                $oBasket->getTsProtectionNet(),
                "Trusted shop total netto price"
            );
            $this->assertEquals(
                $aExpTS['vat'],
                $oBasket->getTsProtectionVat(),
                "Trusted shop total vat price"
            );
        }

        // Vouchers
        $aExpVoucher = $aExpected['totals']['voucher'];
        if (!empty($aExpVoucher)) {
            $this->assertEquals(
                $aExpVoucher['brutto'],
                number_format(round($oBasket->getVoucherDiscValue(), 2), 2, ',', '.'),
                "Voucher total discount brutto"
            );
        }

        // Total netto & brutto, grand total
        $this->assertEquals($aExpected['totals']['totalNetto'], $oBasket->getProductsNetPrice(), "Total Netto");
        $this->assertEquals($aExpected['totals']['totalBrutto'], $oBasket->getFProductsPrice(), "Total Brutto");
        $this->assertEquals($aExpected['totals']['grandTotal'], $oBasket->getFPrice(), "Grand Total");
    }

    /**
     * Basket startup data and expected calculations results
     */
    public function _dpData()
    {
        return $this->_getTestCases($this->_aTestCaseDirs, $this->_aTestCases);
    }

    /**
     * Getting test cases from specified
     *
     * @param array $aDir       directory name
     * @param array $aTestCases of specified test cases
     */
    protected function _getTestCases($aDir, $aTestCases = array())
    {
        // load test cases
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

                $aGlobal["{$sFilename}"] = array($aData);
            }
        }

        return $aGlobal;
    }
}