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
 * Class Integration_Price_OrderNumberingTest
 */
class Integration_Price_OrderNumberingTest extends OxidTestCase
{

    /* Test case directory */
    private $_sTestCaseDir = "testcases/numbering";
    /* Specified test cases (optional) */
    private $_aTestCases = array(
        "numbering_case1.php",
        "numbering_case2.php"
    );

    /**
     * Remove admin user as test fail with sql error: duplicate users.
     *
     * @see OxidTestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        oxDb::getDb()->execute('DELETE FROM `oxuser` WHERE oxusername = \'admin\'');
    }

    /**
     * Truncate tables so counter reset to zero.
     *
     * @see OxidTestCase::tearDown()
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute('TRUNCATE TABLE `oxorder`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxcounters`');

        parent::tearDown();
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
            include($sFilename);
            $aGlobal["{$sFilename}"] = array($aData);
        }

        return $aGlobal;
    }

    /**
     * Order startup data and expected calculations results
     */
    public function _dpData()
    {
        $aData = $this->_getTestCases($this->_sTestCaseDir, $this->_aTestCases);

        return $aData;
    }

    /**
     * Tests order numbering with separateNumbering parameter.
     *
     * @dataProvider _dpData
     */
    public function testOrderNumberingForDifferentShops($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $aOptions = $aTestCase['options'];

        // load calculated basket from provided data
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        $oUser = $oBasket->getBasketUser();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder1 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder1->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder2 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
        // If separate numbering, then it must be restarted.
        $oOrder2->setSeparateNumbering($aOptions['separateNumbering']);

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder2->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        $iOrder1Nr = $oOrder1->oxorder__oxordernr->value;
        $iOrder2Nr = $oOrder2->oxorder__oxordernr->value;
        if ($aOptions['separateNumbering']) {
            $this->assertEquals(1, $iOrder2Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($iOrder1Nr, ($iOrder2Nr - 1), 'Second order must had bigger number if no separate numbering.');
        }
    }

    /**
     * Tests order numbering when middle one is deleted.
     *
     * @dataProvider _dpData
     */
    public function testOrderNumberingForDifferentShops2($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $aOptions = $aTestCase['options'];

        // load calculated basket from provided data
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        $oUser = $oBasket->getBasketUser();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder1 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder1->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder2 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder2->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        $oOrder2->delete();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder3 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder3->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder3->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder3->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
        // If separate numbering, then it must be restarted.
        $oOrder3->setSeparateNumbering($aOptions['separateNumbering']);

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder3->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        $iOrder1Nr = $oOrder1->oxorder__oxordernr->value;
        $iOrder3Nr = $oOrder3->oxorder__oxordernr->value;
        if ($aOptions['separateNumbering']) {
            $this->assertEquals(1, $iOrder3Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($iOrder1Nr, ($iOrder3Nr - 2), 'Second order must had bigger number if no separate numbering.');
        }
    }

    /**
     * Tests order numbering when middle one is saved without finalizing.
     *
     * @dataProvider _dpData
     */
    public function testOrderNumberingForDifferentShops3($aTestCase)
    {
        if ($aTestCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $aOptions = $aTestCase['options'];

        // load calculated basket from provided data
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        $oUser = $oBasket->getBasketUser();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder1 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder1->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder2 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
        $oOrder2->save();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        $oOrder3 = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment'));
        $oOrder3->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $oOrder3->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $oOrder3->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
        // If separate numbering, then it must be restarted.
        $oOrder3->setSeparateNumbering($aOptions['separateNumbering']);

        // if basket has products
        if ($oBasket->getProductsCount()) {
            $iSuccess = $oOrder3->finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false);
        }

        $iOrder1Nr = $oOrder1->oxorder__oxordernr->value;
        $iOrder3Nr = $oOrder3->oxorder__oxordernr->value;
        if ($aOptions['separateNumbering']) {
            $this->assertEquals(1, $iOrder3Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($iOrder1Nr, ($iOrder3Nr - 1), 'Second order must had bigger number if no separate numbering.');
        }
    }
}