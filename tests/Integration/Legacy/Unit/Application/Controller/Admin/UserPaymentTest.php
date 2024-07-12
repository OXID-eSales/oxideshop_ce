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
class UserPaymentTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxuserpayments');
        parent::tearDown();
    }

    /**
     * Resting user_payment::render()
     */
    public function testRender()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, ["getSelUserPayment", "getPaymentId", "getPaymentTypes", "getUser", "getUserPayments", "allowAdminEdit"]);
        $oView->expects($this->once())->method('getSelUserPayment')->will($this->returnValue("getSelUserPayment"));
        $oView->expects($this->once())->method('getPaymentId')->will($this->returnValue("getPaymentId"));
        $oView->expects($this->once())->method('getPaymentTypes')->will($this->returnValue("getPaymentTypes"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue("getUser"));
        $oView->expects($this->once())->method('getUserPayments')->will($this->returnValue("getUserPayments"));
        $oView->expects($this->once())->method('allowAdminEdit')->will($this->returnValue(false));
        $this->assertEquals("user_payment", $oView->render());
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
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxUtils', 'assignValuesToText', '{}');
        oxTestModules::addFunction('oxuserpayment', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", ["oxuserpayments__oxid" => "-1"]);
        $this->setRequestParameter("dynvalue", "testId");

        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, ["allowAdminEdit"]);
            $oView->expects($this->once())->method('allowAdminEdit')->will($this->returnValue(true));
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in user_payment::save()");

            return;
        }

        $this->fail("Error in user_payment::save()");
    }

    /**
     * Resting user_payment::delete()
     */
    public function testDelete()
    {
        oxTestModules::addFunction('oxuserpayment', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuserpayment', 'delete', '{ throw new Exception( "delete" ); }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", ["oxuserpayments__oxid" => "testId"]);

        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserPayment::class, ["allowAdminEdit"]);
            $oView->expects($this->once())->method('allowAdminEdit')->will($this->returnValue(true));
            $oView->delPayment();
        } catch (Exception $exception) {
            $this->assertEquals("delete", $exception->getMessage(), "Error in user_payment::delPayment()");

            return;
        }

        $this->fail("Error in user_payment::delPayment()");
    }

    /**
     * Test getUser()
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
     */
    public function testGetPaymentId()
    {
        $this->setRequestParameter('oxpaymentid', 'oxidinvoice');
        $oUserView = oxNew("user_payment");
        $this->assertEquals('oxidinvoice', $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentId(). If not selected payment
     */
    public function testGetPaymentIdNotSelected()
    {
        $this->setRequestParameter('oxpaymentid', null);
        $oUserView = oxNew("user_payment");
        $this->assertEquals(-1, $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentId(). If not selected payment, but first user payment is set
     */
    public function testGetPaymentIdFromUserPayment()
    {
        $this->setRequestParameter('oxpaymentid', null);
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxid = new oxField('oxidinvoice');
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getUserPayments']);
        $oUser->expects($this->once())->method('getUserPayments')->will($this->returnValue([$oUserPayment]));
        $oUserView = $this->getProxyClass('user_payment');
        $oUserView->setNonPublicVar("_oActiveUser", $oUser);
        $this->assertEquals('oxidinvoice', $oUserView->getPaymentId());
    }

    /**
     * Test getPaymentTypes().
     */
    public function testGetPaymentTypesIsCorrect()
    {
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if (($oPayment->oxpayments__oxdesc->value = 'Rechnung') !== '') {
                $blIsLoaded = true;
            }
        }

        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getPaymentTypes().
     */
    public function testGetPaymentTypesAmountIsCorrect()
    {
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();

        $payments = $oPaymentList->getArray();
        $paymentsIds = '';
        foreach ($payments as $payment) {
            $paymentsIds .= $payment->getId() . "\n";
        }

        $this->assertEquals(5, $oPaymentList->count(), $paymentsIds);
    }

    /**
     * Test getPaymentTypes(). #1464: User administration: Payment methods to assign appear in German
     * although you chose another admin language
     */
    public function testGetPaymentTypesInOtherLang()
    {
        oxTestModules::addFunction("oxLang", "getTplLanguage", "{ return 1; }");
        $oUserView = $this->getProxyClass('user_payment');
        $oPaymentList = $oUserView->getPaymentTypes();
        $blIsLoaded = false;
        foreach ($oPaymentList as $oPayment) {
            if (($oPayment->oxpayments__oxdesc->value = 'Invoice') !== '') {
                $blIsLoaded = true;
            }
        }

        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getSelUserPayment().
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
            if (($oPayment->oxpayments__oxdesc->value = 'Rechnung') !== '') {
                $blIsLoaded = true;
            }
        }

        $this->assertTrue($blIsLoaded);
    }

    /**
     * Test getUserPayments(). #1464: User administration: Payment methods to assign appear in German
     * although you chose another admin language
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
            if (($oPayment->oxpayments__oxdesc->value = 'Invoice') !== '') {
                $blIsLoaded = true;
            }
        }

        $this->assertTrue($blIsLoaded);
    }
}
