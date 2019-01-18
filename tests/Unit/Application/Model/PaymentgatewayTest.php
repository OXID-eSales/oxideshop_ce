<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \OxidEsales\Eshop\Application\Model\Order;
use \oxpaymentgateway;
use \oxField;
use \oxDb;

class mod_oxpaymentgateway extends oxpaymentgateway
{
    public function getPaymentInfo()
    {
        return $this->_oPaymentInfo;
    }

    public function setActive()
    {
        $this->_blActive = true;
    }

    public function setError($iNr, $sMsg)
    {
        $this->_iLastErrorNo = $iNr;
        $this->_sLastError = $sMsg;
    }
}

class PaymentGatewayTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sDelete = "Delete from oxuserpayments where oxuserid = 'test'";
        oxDb::getDb()->Execute($sDelete);

        parent::tearDown();
    }

    public function testSetPaymentParams()
    {
        $oUserpayment = oxNew("oxuserpayment");
        $oUserpayment->oxuserpayments__oxuserid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxpaymentsid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxvalue = new oxField("test", oxField::T_RAW);
        $oUserpayment->Save();
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $oUP = $oPaymentGateway->getPaymentInfo();
        $this->assertEquals($oUP->oxuserpayments__oxvalue->value, $oUserpayment->oxuserpayments__oxvalue->value);
    }

    public function testExecuteNotActivePayment()
    {
        $oOrder = oxNew(Order::class);
        $oPaymentGateway = oxNew('oxPaymentGateway');
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);

        $this->assertEquals($blResult, true);
    }

    public function testExecutePaymentWithoutPaymentInfo()
    {
        $oOrder = oxNew(Order::class);
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setActive();
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, false);
    }

    public function testExecutePayment()
    {
        $oOrder = oxNew(Order::class);
        $oUserpayment = oxNew("oxuserpayment");
        $oUserpayment->oxuserpayments__oxuserid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxpaymentsid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxvalue = new oxField("test", oxField::T_RAW);
        $oUserpayment->Save();
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setActive();
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, false);
    }

    public function testExecutePaymentWithEmptyPaymentId()
    {
        $oOrder = oxNew(Order::class);
        $oUserpayment = oxNew("oxuserpayment");
        $oUserpayment->oxuserpayments__oxuserid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxpaymentsid = new oxField("oxempty", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxvalue = new oxField("test", oxField::T_RAW);
        $oUserpayment->Save();
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setActive();
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, true);
    }

    public function testGetLastErrorNo()
    {
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setError(null, null);
        $blResult = $oPaymentGateway->getLastErrorNo();
        $this->assertEquals($blResult, null);
    }

    public function testGetLastSetErrorNo()
    {
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setError(22, "Test Error");
        $blResult = $oPaymentGateway->getLastErrorNo();
        $this->assertEquals($blResult, 22);
    }

    public function testGetLastError()
    {
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setError(null, null);
        $blResult = $oPaymentGateway->getLastError();
        $this->assertEquals($blResult, null);
    }

    public function testGetLastSetError()
    {
        $oPaymentGateway = new mod_oxpaymentgateway();
        $oPaymentGateway->setError(22, "Test Error");
        $blResult = $oPaymentGateway->getLastError();
        $this->assertEquals($blResult, "Test Error");
    }
}
