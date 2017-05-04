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

/**
 * Testing User_Payment class
 */
class Unit_Admin_userPaymentTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuserpayments');
        parent::tearDown();
    }

    /**
     * Resting user_payment::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oView = $this->getMock("user_payment", array("getSelUserPayment", "getPaymentId", "getPaymentTypes", "getUser", "getUserPayments", "_allowAdminEdit"));
        $oView->expects($this->once())->method('getSelUserPayment')->will($this->returnValue("getSelUserPayment"));
        $oView->expects($this->once())->method('getPaymentId')->will($this->returnValue("getPaymentId"));
        $oView->expects($this->once())->method('getPaymentTypes')->will($this->returnValue("getPaymentTypes"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue("getUser"));
        $oView->expects($this->once())->method('getUserPayments')->will($this->returnValue("getUserPayments"));
        $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(false));
        $this->assertEquals("user_payment.tpl", $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue(isset($aViewData['oxpaymentid']));
        $this->assertTrue(isset($aViewData['paymenttypes']));
        $this->assertTrue(isset($aViewData['edituser']));
        $this->assertTrue(isset($aViewData['userpayments']));
        $this->assertTrue(isset($aViewData['readonly']));
    }

    /**
     * Resting user_payment::save()
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxUtils', 'assignValuesToText', '{}');
        oxTestModules::addFunction('oxuserpayment', 'save', '{ throw new Exception( "save" ); }');

        modConfig::setRequestParameter("oxid", "testId");
        modConfig::setRequestParameter("editval", array("oxuserpayments__oxid" => "-1"));
        modConfig::setRequestParameter("dynvalue", "testId");

        try {
            $oView = $this->getMock("user_payment", array("_allowAdminEdit"));
            $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(true));
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in user_payment::save()");

            return;
        }

        $this->fail("Error in user_payment::save()");
    }

    /**
     * Resting user_payment::delete()
     *
     * @return null
     */
    public function testDelete()
    {
        oxTestModules::addFunction('oxuserpayment', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuserpayment', 'delete', '{ throw new Exception( "delete" ); }');

        modConfig::setRequestParameter("oxid", "testId");
        modConfig::setRequestParameter("editval", array("oxuserpayments__oxid" => "testId"));

        try {
            $oView = $this->getMock("user_payment", array("_allowAdminEdit"));
            $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(true));
            $oView->delPayment();
        } catch (Exception $oExcp) {
            $this->assertEquals("delete", $oExcp->getMessage(), "Error in user_payment::delPayment()");

            return;
        }

        $this->fail("Error in user_payment::delPayment()");
    }

    /**
     * Test getUser()
     *
     * @return null
     */
    public function testGetUser()
    {
        modConfig::setRequestParameter('oxid', 'oxdefaultadmin');
        $oUserView = oxNew("user_payment");
        $oUser = $oUserView->getUser();
        $this->assertEquals('oxdefaultadmin', $oUser->getId());
    }

    /**
     * Test getPaymentId()
     *
     * @return null
     */
    public function testGetPaymentId()
    {
        modConfig::setRequestParameter('oxpaymentid', 'oxidinvoice');
        $oUserView = oxNew("user_payment");
        $this->assertEquals('oxidinvoice', $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentId(). If not selected payment
     *
     * @return null
     */
    public function testGetPaymentIdNotSelected()
    {
        modConfig::setRequestParameter('oxpaymentid', null);
        $oUserView = oxNew("user_payment");
        $this->assertEquals(-1, $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentId(). If not selected payment, but first user payment is set
     *
     * @return null
     */
    public function testGetPaymentIdFromUserPayment()
    {
        modConfig::setRequestParameter('oxpaymentid', null);
        $oUserPayment = new oxUserPayment();
        $oUserPayment->oxuserpayments__oxid = new oxField('oxidinvoice');
        $oUser = $this->getMock('oxuser', array('getUserPayments'));
        $oUser->expects($this->once())->method('getUserPayments')->will($this->returnValue(array($oUserPayment)));
        $oUserView = $this->getProxyClass('user_payment');
        $oUserView->setNonPublicVar("_oActiveUser", $oUser);
        $this->assertEquals('oxidinvoice', $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentTypes().
     *
     * @return null
     */
    public function testGetPaymentTypes()
    {
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();
        $this->assertEquals(6, $oPaymentList->count());
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->oxpayments__oxdesc->value = 'Rechnung') {
                $blIsLoaded = true;
            }
        }
        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getPaymentTypes(). #1464: User administration: Payment methods to assign appear in German
     * although you chose another admin language
     *
     * @return null
     */
    public function testGetPaymentTypesInOtherLang()
    {
        oxTestModules::addFunction("oxLang", "getTplLanguage", "{ return 1; }");
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->oxpayments__oxdesc->value = 'Invoice') {
                $blIsLoaded = true;
            }
        }
        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getSelUserPayment().
     *
     * @return null
     */
    public function testGetSelUserPayment()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setId('_testOxId');
        $oUpay->oxuserpayments__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $oUpay->oxuserpayments__oxvalue = new oxField('_testValue', oxField::T_RAW);
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidinvoice', oxField::T_RAW);
        $oUpay->save();
        $oUserView = $this->getProxyClass('user_payment');
        $oUserView->setNonPublicVar("_sPaymentId", '_testOxId');
        $oPayment = $oUserView->getSelUserPayment();
        $this->assertEquals('_testOxId', $oPayment->getId());
    }

    /**
     * Test getUserPayments().
     *
     * @return null
     */
    public function testGetUserPayments()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setId('_testOxId');
        $oUpay->oxuserpayments__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $oUpay->oxuserpayments__oxvalue = new oxField('_testValue', oxField::T_RAW);
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidinvoice', oxField::T_RAW);
        $oUpay->save();
        modConfig::setRequestParameter('oxid', 'oxdefaultadmin');
        $oUserView = $this->getProxyClass('user_payment');
        $oUserView->setNonPublicVar("_sPaymentId", '_testOxId');
        $oPaymentList = $oUserView->getUserPayments();
        $this->assertEquals(1, $oPaymentList->count());
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->oxpayments__oxdesc->value = 'Rechnung') {
                $blIsLoaded = true;
            }
        }
        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getUserPayments(). #1464: User administration: Payment methods to assign appear in German
     * although you chose another admin language
     *
     * @return null
     */
    public function testGetUserPaymentsInOtherLang()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setId('_testOxId');
        $oUpay->oxuserpayments__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $oUpay->oxuserpayments__oxvalue = new oxField('_testValue', oxField::T_RAW);
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidinvoice', oxField::T_RAW);
        $oUpay->save();
        modConfig::setRequestParameter('oxid', 'oxdefaultadmin');
        oxTestModules::addFunction("oxLang", "getTplLanguage", "{ return 1; }");
        $oUserView = $this->getProxyClass('user_payment');
        $oUserView->setNonPublicVar("_sPaymentId", '_testOxId');
        $oPaymentList = $oUserView->getUserPayments();
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->oxpayments__oxdesc->value = 'Invoice') {
                $blIsLoaded = true;
            }
        }
        $this->assertTrue($blIsLoaded);
    }
}
