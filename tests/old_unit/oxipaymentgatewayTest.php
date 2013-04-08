<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

require_once 'OxidTestCase.php';
require_once 'test_config.inc.php';

/**
 * the name of the test buyer is changed randomly to trick the ipayment server, so there is the unlikely case
 * that the name is 2 times the same. In that case a error will occur.
 */

class oxipaymentgateway_Testing extends oxipaymentgateway
{
    public $oPaymentInfo = null;
}

class Unit_oxipaymentgatewayTest extends OxidTestCase {

    //cUrl connection object
    private $oCUrl;

    private $oIpaymentGateway;

    private $sStorageId;
    private $sRet_booknr;

    private $blNoConnection = false;

    private function initcUrlConnection() {
        if (!function_exists('curl_init')){
            $this->markTestSkipped('No CURL extension found.');
        }
        $sUrl = "https://ipayment.de/merchant/99999/processor.php";
        $this->oCUrl = curl_init();
        curl_setopt($this->oCUrl, CURLOPT_URL, $sUrl);
        curl_setopt($this->oCUrl, CURLOPT_RETURNTRANSFER, true); // return into a variable
        curl_setopt($this->oCUrl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->oCUrl, CURLOPT_SSL_VERIFYPEER, false);
    }

    protected function setup() {
        $this->initcUrlConnection();
        oxAddClassModule('oxipaymentgateway_Testing', 'oxipaymentgateway');
        modConfig::getInstance();

        $this->oIpaymentGateway = oxNew( "oxipaymentgateway" );
        //put data into db
        $oDB = oxConfig :: getInstance()->getDB();
        $myConfig = oxConfig :: getInstance();
        $sSQL = "insert into oxobject2ipayment VALUES ('unittest_a','cc','" . $myConfig->getShopID() . "','unittest_d')";
        $oDB->execute("$sSQL");
        $sSQL = "insert into oxpayments (OXID,OXACTIVE,OXDESC) VALUES ('cc','1','ipaymenttest')";
        $oDB->execute("$sSQL");
    }

    protected function tearDown() {
        $myConfig = oxConfig :: getInstance();

        curl_close($this->oCUrl);

        //remove data from db
        $oDB = oxConfig :: getInstance()->getDB();
        $sSQL = "delete from oxobject2ipayment where oxid = 'unittest_a'";
        $oDB->execute("$sSQL");
        $sSQL = "delete from oxpayments where oxid = 'cc' and OXDESC = 'ipaymenttest'";
        $oDB->execute("$sSQL");
        $sSQL = "delete from oxorder";
        $oDB->execute("$sSQL");

        //reset value
        $myConfig->iPayment_blProcInAdmin = false;
        oxRemClassModule('oxipaymentgateway_Testing');
        modConfig::getInstance()->cleanup();

        // restoring time limit
        set_time_limit(0);
    }

    public function test_getPaymentType() {
        $PaymentGateway = & oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");
        $sRes = $PaymentGateway->getPaymentType("cc");
        $this->assertEquals($sRes, "unittest_d");
    }

    public function test_getStatus() {
        $myConfig = oxConfig :: getInstance();
        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");
        $this->assertEquals($oIpaymentGateway->getStatus("TestStatus"), "TestStatus");
        $myConfig->iPayment_blProcInAdmin = true;
        $this->assertEquals($oIpaymentGateway->getStatus("SUCCESS"), "WAITING");
    }

    public function test_getActionMethod() {
        $myConfig = oxConfig :: getInstance();
        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");
        $this->assertEquals($oIpaymentGateway->getActionMethod("TestType"), "authorizeTESTTYPE");

        $myConfig->iPayment_blProcInAdmin = true;
        $this->assertEquals($oIpaymentGateway->getActionMethod("TestType"), "preAuthorizeTESTTYPE");
    }

    public function test_getOrderPrice() {
        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");

        $oIpaymentGateway->oOrder = oxUtils :: getInstance()->oxNew("oxorder", "core");

        $this->assertEquals($oIpaymentGateway->getOrderPrice(1.11), "111");

        $oIpaymentGateway->oOrder->oxorder__oxpaycost->value = 0.2;
        $this->assertEquals($oIpaymentGateway->getOrderPrice(1.11), "20");

        $oIpaymentGateway->oOrder->totalorder = 3.3;
        $this->assertEquals($oIpaymentGateway->getOrderPrice(1.11), "330");
    }

    public function test_debug() {
        $this->markTestSkipped();
    }

    public function test_collectResponseData() {
        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");
        $aTest['key1'] = 'value1';
        $aTest['key2'] = 'value2';
        $sRes = $oIpaymentGateway->collectResponseData($aTest, false);
        $this->assertEquals($sRes, "KEY1: value1\nKEY2: value2\n");

    }

    /**
     * test if a connection to the ipayment server is possible, if not the rest of this test is marked as skipped
     */
    public function test_checkConnection() {
        $oRes = curl_exec($this->oCUrl);
        if ($oRes == false) {
            $this->blNoConnection = true;
            $this->fail('no connection to external ipayment server possible.');
        }
    }

    public function test_ExecutePayment() {
        if ($this->blNoConnection) {
            $this->markTestSkipped('no connection');
        }
        $this->assertTrue($this->doPayment());
    }

    private function setConfigParameter() {
        $myConfig = oxConfig :: getInstance();
        // test data supplied by ipayment.de
        $myConfig->iShopID_iPayment_Account = '99999';
        $myConfig->iShopID_iPayment_User = '99999';
        $myConfig->iShopID_iPayment_Passwort = '0';
        $myConfig->iShopID_iPayment_AdminPass = '5cfgRT34xsdedtFLdfHxj7tfwx24fe';
        $myConfig->iPayment_blActive = true;
    }

    public function test_CaptureOrder() {
        if ($this->blNoConnection) {
            $this->markTestSkipped('no connection');
        }

        //first register and reserve a payment
        $this->doPreAuth();

        $this->setConfigParameter();

        //order
        $oOrder = oxUtils :: getInstance()->oxNew("oxorder", "core");
        $oOrder->oxorder__oxcurrency->value = "EUR";
        $this->oIpaymentGateway->oPaymentInfo->oxuserpayments__oxpaymentsid->value = "cc";
        $oOrder->oxorder__oxtransid->value = $this->sRet_booknr;

        //then capture that reservation
        $blRes = $this->oIpaymentGateway->CaptureOrder("10.00", $oOrder);
        $this->assertTrue($blRes);
    }

    public function test_CancelOrder() {
        if ($this->blNoConnection) {
            $this->markTestSkipped('no connection');
        }

        // first register and reserve a payment
        $this->doPreAuth();
        //then cancel that reservation
        $this->assertTrue($this->cancelOrder());

    }

    public function test_RefundOrder() {
        if ($this->blNoConnection) {
            $this->markTestSkipped('no connection');
        }
        //first check paymentdetails and store them at ipayment server
        $this->doPaymentBaseCheck();
        //then do the payment
        $this->assertTrue($this->doPayment());

        $this->setConfigParameter();

        //order
        $oOrder = oxUtils :: getInstance()->oxNew("oxorder", "core");
        $oOrder->oxorder__oxcurrency->value = "EUR";

        $this->oIpaymentGateway->oPaymentInfo->oxuserpayments__oxpaymentsid->value = "cc";
        $oOrder->oxorder__oxtransid->value = $this->sRet_booknr;
        $blRes = $this->oIpaymentGateway->RefundOrder("10.00", $oOrder);
        $this->assertTrue($blRes);
    }

    public function test_catchnow() {
        $this->markTestSkipped();
    }

    private function getParameters() {
        $adata['trxuser_id'] = '99999';
        $adata['trxpassword'] = '0';
        $adata['adminactionpassword'] = '5cfgRT34xsdedtFLdfHxj7tfwx24fe';
        $adata['trx_currency'] = 'EUR';
        $adata['trx_amount'] = '1000';

        $adata['trx_paymenttyp'] = 'cc';
        //random letters added to trick the ipayment server
        $adata['addr_name'] = 'Hans Test' . chr(rand(98, 121)) . chr(rand(98, 121));
        $adata['addr_check_address'] = '0';
        $adata['addr_address_needed'] = '0';
        $adata['gateway'] = '1';
        $adata['cc_number'] = '5105105105105100';
        $adata['cc_expdate_month'] = '10';
        $adata['cc_expdate_year'] = '10';
        $adata['use_datastorage'] = '1';
        $adata['from_ip'] = '127.0.0.1';
        return $adata;
    }

    //inserts a record at ipayment server
    public function doPaymentBaseCheck() {
        //values supplied by www.ipayment.de

        $adata = $this->getParameters();
        $adata['trx_typ'] = 'base_check';
        curl_setopt($this->oCUrl, CURLOPT_POSTFIELDS, $adata);

        $sRes = curl_exec($this->oCUrl);
        $sSearch = 'Status=0';
        //no error returned
        $this->assertTrue(stripos($sRes, $sSearch) >= 0, "Test record could not be checked and stored in ipaymentserver");

        //store the storage_id
        $sSearch = "&storage_id=";
        //because the id is the last parameter returned!
        $this->sStorageId = substr($sRes, stripos($sRes, $sSearch) + 12);

        //ipayment transaction number
        $sSearch = "&ret_booknr=";
        $iStartpos = stripos($sRes, $sSearch) + 12;
        $iEndpos = stripos($sRes, "&", $iStartpos);
        $ilength = $iEndpos - $iStartpos;
        $this->sRet_booknr = substr($sRes, $iStartpos, $ilength);
    }

    public function doPreAuth() {
        //values supplied by www.ipayment.de

        $adata = $this->getParameters();
        $adata['trx_typ'] = 'preauth';

        curl_setopt($this->oCUrl, CURLOPT_POSTFIELDS, $adata);

        $sRes = curl_exec($this->oCUrl);
        $sSearch = 'Status=0';
        //no error returned
        $this->assertTrue(stripos($sRes, $sSearch) >= 0, "No PreAuth possible");

        //store the storage_id
        $sSearch = "&storage_id=";
        //because the id is the last parameter returned!
        $this->sStorageId = substr($sRes, stripos($sRes, $sSearch) + 12);

        //ipayment transaction number
        $sSearch = "&ret_booknr=";
        $iStartpos = stripos($sRes, $sSearch) + 12;
        $iEndpos = stripos($sRes, "&", $iStartpos);
        $ilength = $iEndpos - $iStartpos;
        $this->sRet_booknr = substr($sRes, $iStartpos, $ilength);

    }

    public function doPayment() {
        $this->doPaymentBaseCheck();

        $this->setConfigParameter();

        $mySession = OxSession :: getInstance();

        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");

        //order
        $oOrder = oxUtils :: getInstance()->oxNew("oxorder", "core");
        $oOrder->oxorder__oxcurrency->value = "EUR";
        $aIpayment["trx_paymenttyp"] = "cc";
        $aIpayment["storage_id"] = $this->sStorageId;
        $mySession->setVar("aiPaymentData", $aIpayment);
        $oIpaymentGateway->oPaymentInfo->oxuserpayments__oxpaymentsid->value = "cc";
        $blRes = $oIpaymentGateway->executePayment("10.00", $oOrder);

        $this->sRet_booknr = $oOrder->oxorder__oxtransid->value;

        return $blRes;
    }

    public function cancelOrder() {

        $this->setConfigParameter();

        $oIpaymentGateway = oxUtils :: getInstance()->oxNew("oxipaymentgateway", "core");

        //order
        $oOrder = oxUtils :: getInstance()->oxNew("oxorder", "core");
        $oOrder->oxorder__oxcurrency->value = "EUR";
        $oIpaymentGateway->oPaymentInfo->oxuserpayments__oxpaymentsid->value = "cc";

        $oOrder->oxorder__oxtransid->value = $this->sRet_booknr;

        $blRes = $oIpaymentGateway->CancelOrder("10.00", $oOrder);
        return $blRes;
    }

}