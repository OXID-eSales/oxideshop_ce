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
namespace Unit\Application\Controller\Admin;

use \oxorder;
use \oxuserpayment;

use \oxField;
use \Exception;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Order_Overview class
 */
class OrderOverviewTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable("oxorderarticles");
        parent::tearDown();
    }

    /**
     * Order_Overview::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Order_Overview');
        $this->assertEquals('order_overview.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxorder);
    }

    /**
     * Order_Overview::GetPaymentType() test case
     *
     * @return null
     */
    public function testGetPaymentType()
    {
        oxTestModules::addFunction('oxpayment', 'load', '{ $this->oxpayments__oxdesc = new oxField("testValue"); return true; }');

        // defining parameters
        $oOrder = $this->getMock("oxorder", array("getPaymentType"));
        $oOrder->oxorder__oxpaymenttype = new oxField("testValue");

        $oView = oxNew('Order_Overview');
        $oUserPayment = $oView->UNITgetPaymentType($oOrder);

        $this->assertTrue($oUserPayment instanceof oxuserpayment);
        $this->assertEquals("testValue", $oUserPayment->oxpayments__oxdesc->value);
    }

    /**
     * Order_Overview::Sendorder() test case
     *
     * @return null
     */
    public function testSendorder()
    {
        $this->setRequestParameter("sendmail", true);
        oxTestModules::addFunction('oxemail', 'sendSendedNowMail', '{ throw new Exception( "sendSendedNowMail" ); }');
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'save', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'getOrderArticles', '{ return array(); }');

        // testing..
        try {
            $oView = oxNew('Order_Overview');
            $oView->sendorder();
        } catch (Exception $oExcp) {
            $this->assertEquals("sendSendedNowMail", $oExcp->getMEssage(), "Error in Order_Overview::sendorder()");

            return;
        }
        $this->fail("Error in Order_Overview::sendorder()");
    }

    /**
     * Order_Overview::Resetorder() test case
     *
     * @return null
     */
    public function testResetorder()
    {
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'save', '{ throw new Exception( $this->oxorder__oxsenddate->value ); }');

        // testing..
        try {
            $oView = oxNew('Order_Overview');
            $oView->resetorder();
        } catch (Exception $oExcp) {
            $this->assertEquals("0000-00-00 00:00:00", $oExcp->getMessage(), "Error in Order_Overview::resetorder()");

            return;
        }
        $this->fail("Error in Order_Overview::resetorder()");
    }

    /**
     * Order shipping date reset test case
     *
     * @return null
     */
    public function testCanReset()
    {
        $soxId = '_testOrderId';
        // writing test order
        $oOrder = oxNew("oxorder");
        $oOrder->setId($soxId);
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField("oxdefaultadmin");
        $oOrder->oxorder__oxbillcompany = new oxField("Ihr Firmenname");
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField("Hans");
        $oOrder->oxorder__oxbilllname = new oxField("Musterm0ann");
        $oOrder->oxorder__oxbillstreet = new oxField("Musterstr");
        $oOrder->oxorder__oxstorno = new oxField("0");
        $oOrder->oxorder__oxsenddate = new oxField("0000-00-00 00:00:00");
        $oOrder->save();

        $oView = oxNew('Order_Overview');

        $this->setRequestParameter("oxid", $soxId);
        $this->assertFalse($oView->canResetShippingDate());

        $oOrder->oxorder__oxsenddate = new oxField(date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime()));
        $oOrder->save();

        $this->assertTrue($oView->canResetShippingDate());

        $oOrder->oxorder__oxstorno = new oxField("1");
        $oOrder->save();

        $this->assertFalse($oView->canResetShippingDate());

        $oOrder->oxorder__oxsenddate = new oxField("0000-00-00 00:00:00");
        $oOrder->save();

        $this->assertFalse($oView->canResetShippingDate());
    }


    /**
     * Provide name, and correct expected name for testMakeValidFileName
     *
     * @return array
     */
    public function nameProvider()
    {
        return array(
            array('abc', 'abc'),
            array('ab/c', 'abc'),
            array('ab!@#$%^&*()c', 'abc'),
            array('ab_!_@_c', 'ab___c'),
            array('ab_!_@_c      s', 'ab___c_s'),
            array('      s', '_s'),
            array('!@#$%^&*()_+//////\\', '_'),
            array(null, null),
        );
    }

    /**
     * Check if valid name is being generated
     *
     * @dataProvider nameProvider
     *
     * @param $sName
     * @param $sExpectedValidaName
     *
     * @return null
     */
    public function testMakeValidFileName($sName, $sExpectedValidaName)
    {
        $oMyOrder = oxNew('Order_Overview');
        $this->assertEquals($sExpectedValidaName, $oMyOrder->makeValidFileName($sName));
    }
}
