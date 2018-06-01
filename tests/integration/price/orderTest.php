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
 * @copyright (C) OXID eSales AG 2003-2018
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basketconstruct.php';

/**
 * Final order calculation test
 * Known action cycle to test:
 * 1.) Save basket
 * 2.) Proceed order
 * 3.) Change order in different ways
 *     a.) By articles quantity
 *     b.) By discount amount
 *     c.) By adding / removing articles
 * 4.) Recalculate
 */
class Integration_Price_OrderTest extends OxidTestCase
{

    /* Test case directory */
    private $_sTestCaseDir = "testcases/order";
    /* Specified test cases (optional) */
    private $_aTestCases = array(//"testCase.php"
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
        $oDb->query("TRUNCATE oxarticles");
        $oDb->query("TRUNCATE oxcategories");
        $oDb->query("TRUNCATE oxdiscount");
        $oDb->query("TRUNCATE oxobject2discount");
        $oDb->query("TRUNCATE oxwrapping");
        $oDb->query("TRUNCATE oxdelivery");
        $oDb->query("TRUNCATE oxdel2delset");
        $oDb->query("TRUNCATE oxobject2payment");
        $oDb->query("TRUNCATE oxobject2category");
        $oDb->query("TRUNCATE oxvouchers");
        $oDb->query("TRUNCATE oxvoucherseries");
        $oDb->query("TRUNCATE oxuser");
        $oDb->query("TRUNCATE oxdeliveryset");
        $oDb->query("TRUNCATE oxpayments");
        $oDb->query("TRUNCATE oxprice2article");
    }


    /**
     * Order startup data and expected calculations results
     */
    public function _dpData()
    {
        return $this->_getTestCases($this->_sTestCaseDir, $this->_aTestCases);
    }

    /**
     * Tests order calculations
     *
     * @dataProvider _dpData
     */
    public function testOrderCalculation($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }
        // expectations
        $aExpected = $aTestCase['expected'];
        // actions
        $aActions = $aTestCase['actions'];

        // load calculated basket from provided data
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        $oUser = $oBasket->getBasketUser();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));


        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        $this->assertEquals(0, $iSuccess);

        // check order totals
        $this->_checkTotals($aExpected, 1, $oOrder);
        if (!empty($aActions)) {
            foreach ($aActions as $_sFunction => $aParams) {
                $this->$_sFunction($aParams, $oOrder);
            }
            oxRegistry::set("oxDeliveryList", null);
            $oOrder->recalculateOrder();
            $this->_checkTotals($aExpected, 2, $oOrder);
        }
    }

    /**
     * Check totals of saved (recalculated) order
     *
     * @param array   $aExpected
     * @param object  $oOrder
     * @param integer $iApproach number of order recalculate events starting at 1
     */
    protected function _checkTotals($aExpected, $iApproach, $oOrder)
    {
        $aExpTotals = $aExpected[$iApproach]['totals'];
        $aArticles = $aExpected[$iApproach]['articles'];
        $blIsNettoMode = $oOrder->isNettoMode();
        $aOrderArticles = $oOrder->getOrderArticles();

        foreach ($aOrderArticles as $oArticle) {
            $iArtId = $oArticle->oxorderarticles__oxartid->value;
            if ($blIsNettoMode) {
                $sUnitPrice = $oArticle->getNetPriceFormated();
                $sTotalPrice = $oArticle->getTotalNetPriceFormated();
            } else {
                $sUnitPrice = $oArticle->getBrutPriceFormated();
                $sTotalPrice = $oArticle->getTotalBrutPriceFormated();
            }
            $this->assertEquals($aArticles[$iArtId][0], $sUnitPrice, "#{$iApproach} Unit price of order art no #{$iArtId}");
            $this->assertEquals($aArticles[$iArtId][1], $sTotalPrice, "#{$iApproach} Total price of order art no #{$iArtId}");
        }

        $aProductVats = $oOrder->getProductVats(true);

        $this->assertEquals($aExpTotals['totalNetto'], $oOrder->getFormattedTotalNetSum(), "Product Net Price #$iApproach");
        $this->assertEquals($aExpTotals['discount'], $oOrder->getFormattedDiscount(), "Discount #$iApproach");

        if ($aProductVats) {
            foreach ($aProductVats as $iVat => $dVatPrice) {
                $this->assertEquals($aExpTotals['vats'][$iVat], $dVatPrice, "Vat %{$iVat} total cost #$iApproach");
            }
        }

        $this->assertEquals($aExpTotals['totalBrutto'], $oOrder->getFormattedTotalBrutSum(), "Product Gross Price #$iApproach");

        $aExpTotals['voucher']
            ? $this->assertEquals($aExpTotals['voucher']['brutto'], $oOrder->getFormattedTotalVouchers(), "Voucher costs #$iApproach")
            : '';

        $aExpTotals['delivery']
            ? $this->assertEquals($aExpTotals['delivery']['brutto'], $oOrder->getFormattedeliveryCost(), "Shipping costs #$iApproach")
            : '';

        $aExpTotals['wrapping']
            ? $this->assertEquals($aExpTotals['wrapping']['brutto'], $oOrder->getFormattedWrapCost(), "Wrapping costs #$iApproach")
            : '';

        $aExpTotals['giftcard']
            ? $this->assertEquals($aExpTotals['giftcard']['brutto'], $oOrder->getFormattedGiftCardCost(), "Giftcard costs #$iApproach")
            : '';

        $aExpTotals['payment']
            ? $this->assertEquals($aExpTotals['payment']['brutto'], $oOrder->getFormattedPayCost(), "Charge Payment Method #$iApproach")
            : '';
        $this->assertEquals($aExpTotals['grandTotal'], $oOrder->getFormattedTotalOrderSum(), "Sum total #$iApproach");
    }

    /**
     * Getting test cases from specified
     *
     * @param string $sDir       directory name
     * @param array  $aTestCases of specified test cases
     */
    protected function _getTestCases($sDir, $aTestCases = array())
    {
        $sPath = __DIR__ ."/" . $sDir . "/";
        // load test cases
        $aGlobal = array();
        if (empty($aTestCases)) {
            $aFiles = glob($sPath . "*.php", GLOB_NOSORT);
        } else {
            foreach ($aTestCases as $sTestCase) {
                $aFiles[] = $sPath . $sTestCase;
            }
        }
        foreach ($aFiles as $sFilename) {
            if (!file_exists($sFilename)) {
                throw new Exception("Test case {$sFilename} does not exist!");
            }
            include($sFilename);
            $aGlobal["{$sFilename}"] = array($aData);
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
        return oxDb::getDb()->execute("TRUNCATE {$sTable}");
    }

    /* --- Expected functions for changing saved order --- */

    /**
     * Change configs
     *
     * @param array $aConfigOptions
     */
    protected function _changeConfigs($aConfigOptions)
    {
        $oConfig = oxRegistry::getConfig();
        if (!empty($aConfigOptions)) {
            foreach ($aConfigOptions as $sKey => $sValue) {
                $oConfig->setConfigParam($sKey, $sValue);
            }
        }
    }

    /**
     * Add articles
     *
     * @param array  $aArticles new articles to add
     * @param object $oOrder
     */
    protected function _addArticles($aArticles, $oOrder)
    {
        $oBasketConstruct = new BasketConstruct();
        $aArts = $oBasketConstruct->getArticles($aArticles);
        foreach ($aArts as $aArt) {
            $oProduct = new oxArticle();
            $oProduct->load($aArt['id']);
            $dAmount = $aArt['amount'];
            $oOrderArticle = oxNew('oxorderArticle');
            $oOrderArticle->oxorderarticles__oxartid = new oxField($oProduct->getId());
            $oOrderArticle->oxorderarticles__oxartnum = new oxField($oProduct->oxarticles__oxartnum->value);
            $oOrderArticle->oxorderarticles__oxamount = new oxField($dAmount);
            $oOrderArticle->oxorderarticles__oxselvariant = new oxField(oxRegistry::getConfig()->getRequestParameter('sel'));
            $oOrder->recalculateOrder(array($oOrderArticle));
        }
    }

    /**
     * Removes articles
     *
     * @param array  $aArtIds article id's to remove
     * @param object $oOrder
     */
    protected function _removeArticles($aArtIds, $oOrder)
    {
        $aArtIdsCount = count($aArtIds);
        $aOrderArticles = $oOrder->getOrderArticles();
        foreach ($aOrderArticles as $oOrderArticle) {
            for ($i = 0; $i < $aArtIdsCount; $i++) {
                if ($oOrderArticle->oxorderarticles__oxartid->value == $aArtIds[$i]) {
                    $oOrderArticle->delete();
                }
            }
        }
    }

    /**
     * Change articles
     *
     * @param array  $aArtIdsAmounts
     * @param object $oOrder
     */
    protected function _changeArticles($aArtIdsAmounts, $oOrder)
    {
        $sArtCount = count($aArtIdsAmounts);
        $aOrderArticles = $oOrder->getOrderArticles();
        foreach ($aOrderArticles as $oOrderArticle) {
            for ($i = 0; $i < $sArtCount; $i++) {
                if ($oOrderArticle->oxorderarticles__oxartid->value == $aArtIdsAmounts[$i]['oxid']) {
                    $oOrderArticle->setNewAmount($aArtIdsAmounts[$i]['amount']);
                }
            }
        }
    }
}