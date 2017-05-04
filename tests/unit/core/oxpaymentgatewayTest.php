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

class Unit_Core_oxpaymentgatewayTest extends OxidTestCase
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
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $oUP = $oPaymentGateway->getPaymentInfo();
        $this->assertEquals($oUP->oxuserpayments__oxvalue->value, $oUserpayment->oxuserpayments__oxvalue->value);
    }

    public function testExecuteNotActivePayment()
    {
        $oOrder = new stdClass();
        $oPaymentGateway = oxNew('oxPaymentGateway');
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);

        $this->assertEquals($blResult, true);
    }

    public function testExecutePaymentWithoutPaymentInfo()
    {
        $oOrder = new stdClass();
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setActive();
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, false);
    }

    public function testExecutePayment()
    {
        $oOrder = new stdClass();
        $oUserpayment = oxNew("oxuserpayment");
        $oUserpayment->oxuserpayments__oxuserid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxpaymentsid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxvalue = new oxField("test", oxField::T_RAW);
        $oUserpayment->Save();
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setActive();
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, false);
    }

    public function testExecutePaymentWithEmptyPaymentId()
    {
        $oOrder = new stdClass();
        $oUserpayment = oxNew("oxuserpayment");
        $oUserpayment->oxuserpayments__oxuserid = new oxField("test", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxpaymentsid = new oxField("oxempty", oxField::T_RAW);
        $oUserpayment->oxuserpayments__oxvalue = new oxField("test", oxField::T_RAW);
        $oUserpayment->Save();
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setActive();
        $oPaymentGateway->setPaymentParams($oUserpayment);
        $blResult = $oPaymentGateway->executePayment(2, $oOrder);
        $this->assertEquals($blResult, true);
    }

    public function testGetLastErrorNo()
    {
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setError(null, null);
        $blResult = $oPaymentGateway->getLastErrorNo();
        $this->assertEquals($blResult, null);
    }

    public function testGetLastSetErrorNo()
    {
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setError(22, "Test Error");
        $blResult = $oPaymentGateway->getLastErrorNo();
        $this->assertEquals($blResult, 22);
    }

    public function testGetLastError()
    {
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setError(null, null);
        $blResult = $oPaymentGateway->getLastError();
        $this->assertEquals($blResult, null);
    }

    public function testGetLastSetError()
    {
        $oPaymentGateway = new mod_oxpaymentgateway;
        $oPaymentGateway->setError(22, "Test Error");
        $blResult = $oPaymentGateway->getLastError();
        $this->assertEquals($blResult, "Test Error");
    }
}
