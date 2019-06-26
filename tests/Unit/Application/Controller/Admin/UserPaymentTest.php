<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxField;
use \oxTestModules;

/**
 * Testing User_Payment class
 */
class UserPaymentTest extends \OxidTestCase
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, array("getSelUserPayment", "getPaymentId", "getPaymentTypes", "getUser", "getUserPayments", "_allowAdminEdit"));
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

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", array("oxuserpayments__oxid" => "-1"));
        $this->setRequestParameter("dynvalue", "testId");

        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, array("_allowAdminEdit"));
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

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", array("oxuserpayments__oxid" => "testId"));

        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, array("_allowAdminEdit"));
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
        $this->setRequestParameter('oxid', 'oxdefaultadmin');
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
        $this->setRequestParameter('oxpaymentid', 'oxidinvoice');
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
        $this->setRequestParameter('oxpaymentid', null);
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
        $this->setRequestParameter('oxpaymentid', null);
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxid = new oxField('oxidinvoice');
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getUserPayments'));
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
    public function testGetPaymentTypesIsCorrect()
    {
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->oxpayments__oxdesc->value = 'Rechnung') {
                $blIsLoaded = true;
            }
        }
        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getPaymentTypes().
     *
     * @return null
     */
    public function testGetPaymentTypesAmountIsCorrect()
    {
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();

        $payments = $oPaymentList->getArray();
        $paymentsIds = '';
        foreach ($payments as $payment) {
            $paymentsIds .= $payment->getId() ."\n";
        }

        $this->assertEquals(6, $oPaymentList->count(), $paymentsIds);
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
        $this->setRequestParameter('oxid', 'oxdefaultadmin');
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
        $this->setRequestParameter('oxid', 'oxdefaultadmin');
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
