<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \stdClass;
use \oxDb;
use \oxRegistry;

/**
 * Testing oxuserpayment class
 */
class UserpaymentTest extends \OxidTestCase
{
    protected $_oUpay = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_oUpay = oxNew('oxuserpayment');
        $this->_oUpay->setId('_testOxId');
        $this->_oUpay->oxuserpayments__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $this->_oUpay->oxuserpayments__oxvalue = new oxField('_testValue', oxField::T_RAW);
        $this->_oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidcashondel', oxField::T_RAW);
        $this->_oUpay->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oUpay->delete('_testOxId');
        $this->_oUpay->delete('_testOxId2');
        $this->cleanUpTable('oxuserpayments');
        $this->cleanUpTable('oxorder');

        parent::tearDown();
    }

    public function testGetDynValuesIfAlwaysArrayIsReturned()
    {
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxvalue = new oxField('lsbankname__12613212@@lsblz__132132132@@lsktonr__1331321321@@lsktoinhaber__Dainius O&quot;bryan@@');

        $this->assertTrue(is_array($oUserPayment->getDynValues()));

        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxvalue = new oxField('some unknown format value');

        $this->assertTrue(is_array($oUserPayment->getDynValues()));

        $oUserPayment = oxNew('oxUserPayment');
        $this->assertTrue(is_array($oUserPayment->getDynValues()));
    }

    /**
     * Test case:
     * After order is made, check the Customer's First and Last names in the
     * Orders->Overview tab (order_overview.php, general Order info):
     * "Dainius O'bryan" is displayed as "Dainius &039;bryan".
     */
    public function testGetDynValuesForTestCase()
    {
        $aDynValues = array();

        $oVal = new stdClass();
        $oVal->name = 'lsbankname';
        $oVal->value = '12613212';
        $aDynValues[] = $oVal;

        $oVal = new stdClass();
        $oVal->name = 'lsblz';
        $oVal->value = '132132132';
        $aDynValues[] = $oVal;

        $oVal = new stdClass();
        $oVal->name = 'lsktonr';
        $oVal->value = '1331321321';
        $aDynValues[] = $oVal;

        $oVal = new stdClass();
        $oVal->name = 'lsktoinhaber';
        $oVal->value = 'Dainius O&quot;bryan';
        $aDynValues[] = $oVal;

        $oUserPayment = oxNew('oxuserpayment');
        $oUserPayment->oxuserpayments__oxvalue = new oxField('lsbankname__12613212@@lsblz__132132132@@lsktonr__1331321321@@lsktoinhaber__Dainius O&quot;bryan@@');
        $this->assertEquals($aDynValues, $oUserPayment->getDynValues());
    }

    /**
     * Checking if payment encryption key is good
     */
    public function testSetDynValues()
    {
        $oUserPayment = $this->getProxyClass('oxUserPayment');
        $oUserPayment->setDynValues('aDynValues');
        $oUserPayment->getNonPublicVar('_aDynValues', 'aDynValues');
    }


    /**
     * Checking if payment encryption key is good
     */
    public function testGetaDynValues()
    {
        $sDyn = 'kktype__visa@@kknumber__12345@@kkmonth__11@@kkyear__2008@@kkname__testName@@kkpruef__56789@@';
        $aDynVal = oxRegistry::getUtils()->assignValuesFromText($sDyn);
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxvalue = new oxField($sDyn, oxField::T_RAW);

        $this->assertEquals($aDynVal, $oUserPayment->aDynValues);
    }

    /**
     * Testing if object type is correct and payment key is correctly generated
     */
    public function testOxUserPayment()
    {
        $oUpay = oxNew('oxuserpayment');
        $this->assertEquals('oxuserpayment', $oUpay->getClassName());
        $this->assertEquals(oxRegistry::getUtils()->strRot13('fq45QS09_fqyx09239QQ'), $oUpay->getPaymentKey('_sPaymentKey'));
    }

    /**
     * Testing if constructor sets _blStoreCreditCardInfo from oxConfig param
     */
    public function testConstructor()
    {
        $this->getConfig()->setConfigParam('blStoreCreditCardInfo', true);

        $oUpay = oxNew('oxuserpayment');
        $oUpay->__construct();

        $this->assertTrue($oUpay->getStoreCreditCardInfo());
    }

    /**
     * Checking if payment encryption key is good
     */
    public function testGetPaymentDesc()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->load('_testOxId');
        $this->assertEquals('Nachnahme', $oUpay->oxpayments__oxdesc->value);
    }

    /**
     * Checking if payment encryption key is good
     */
    public function testGetPaymentKey()
    {
        $oUpay = oxNew('oxuserpayment');
        $this->assertEquals(oxRegistry::getUtils()->strRot13('fq45QS09_fqyx09239QQ'), $oUpay->getPaymentKey());
    }

    /**
     * Testing if loader decodes oxvalue field
     */
    public function testLoadDecodesValue()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->load('_testOxId');

        $this->assertEquals('_testValue', $oUpay->oxuserpayments__oxvalue->value);
    }

    /**
     * Testing if insert encodes oxvalue field
     */
    public function testInsertEncodesOxValue()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setId('_testOxId2');
        $oUpay->oxuserpayments__oxvalue = new oxField('123456789', oxField::T_RAW);
        $oUpay->save();

        $this->assertEquals("\xbc\x69\x65\xb9\x3a\x13\x29\xa1\xeb", oxDb::getDb()->getOne("SELECT oxvalue FROM oxuserpayments WHERE oxid='_testOxId2'"));
    }

    /**
     * Testing if insert is not exectuted when _blStoreCreditCardInfo is false
     * and payment type is credit card
     */
    public function testInsertWhenStoreCreditCardInfoIsOffAndPaymentTypeIsCreditCard()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setStoreCreditCardInfo(false);
        $oUpay->setId('_testOxId2');
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidcreditcard', oxField::T_RAW);
        $oUpay->save();

        $this->assertFalse(oxDb::getDb()->getOne("SELECT oxid FROM oxuserpayments WHERE oxid='_testOxId2'"));
    }

    /**
     * Testing if insert is exectuted when _blStoreCreditCardInfo is true
     * and payment type is credit card
     */
    public function testInsertWhenStoreCreditCardInfoIsOnAndPaymentTypeIsCreditCard()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setStoreCreditCardInfo(true);
        $oUpay->setId('_testOxId2');
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidcreditcard', oxField::T_RAW);
        $oUpay->save();

        $this->assertEquals('_testOxId2', oxDb::getDb()->getOne("SELECT oxid FROM oxuserpayments WHERE oxid='_testOxId2'"));
    }

    /**
     * Testing if insert is exectuted when _blStoreCreditCardInfo is false
     * and payment type is not credit card
     */
    public function testInsertWhenStoreCreditCardInfoIsOnAndPaymentTypeIsNotCreditCard()
    {
        $oUpay = oxNew('oxuserpayment');
        $oUpay->setStoreCreditCardInfo(true);
        $oUpay->setId('_testOxId2');
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxidinvoice', oxField::T_RAW);
        $oUpay->save();

        $this->assertEquals('_testOxId2', oxDb::getDb()->getOne("SELECT oxid FROM oxuserpayments WHERE oxid='_testOxId2'"));
    }

    /**
     * Testing if update encodes oxvalue field
     */
    public function testUpdateEncodesOxValue()
    {
        $this->_oUpay->oxuserpayments__oxvalue = new oxField('123456789', oxField::T_RAW);
        $this->_oUpay->save();
        $this->assertEquals("BC6965B93A1329A1EB", oxDb::getDb()->getOne("SELECT hex(oxvalue) FROM oxuserpayments WHERE oxid='_testOxId'"));
    }

    /**
     * Testing setter/getter for _blStoreCreditCardInfo
     */
    public function testSetGetStoreCreditCardInfo()
    {
        $this->_oUpay->setStoreCreditCardInfo(true);
        $this->assertTrue($this->_oUpay->getStoreCreditCardInfo());

        $this->_oUpay->setStoreCreditCardInfo(false);
        $this->assertFalse($this->_oUpay->getStoreCreditCardInfo());
    }

    /**
     * Inserting test orders
     */
    protected function _insertTestOrders($aUserPaymentId, $sUserId)
    {
        $oDb = oxDb::getDb();

        $sQ = "INSERT INTO `oxorder`
                   (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLCOMPANY`, `OXBILLEMAIL`, `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLADDINFO`, `OXBILLUSTID`, `OXBILLCITY`, `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLFAX`, `OXBILLSAL`, `OXDELCOMPANY`, `OXDELFNAME`, `OXDELLNAME`, `OXDELSTREET`, `OXDELSTREETNR`, `OXDELADDINFO`, `OXDELCITY`, `OXDELCOUNTRYID`, `OXDELSTATEID`, `OXDELZIP`, `OXDELFON`, `OXDELFAX`, `OXDELSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXARTVAT2`, `OXARTVATPRICE2`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXCARDID`, `OXCARDTEXT`, `OXDISCOUNT`, `OXEXPORT`, `OXBILLNR`, `OXTRACKCODE`, `OXSENDDATE`, `OXREMARK`, `OXVOUCHERDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`, `OXTRANSID`, `OXPAYID`, `OXXID`, `OXPAID`, `OXSTORNO`, `OXIP`, `OXTRANSSTATUS`, `OXLANG`, `OXINVOICENR`, `OXDELTYPE`)
               VALUES
                   (?, ?, ?, ?, ?, '', 'user@oxid-esales.com', 'Marc', 'Muster', 'Hauptstr.', '13', '', '', 'Freiburg', 'a7c40f631fc920687.20179984', 'BW', '79098', '', '', 'MR', '', '', '', '', '', '', '', '', '', '', '', '', '', ?, 'oxidinvoice', 1639.15, 2108.39, 1950.59, 19, 311.44, 0, 0, 0, 19, 0, 0, 0, 0, '', '', 157.8, 0, '', '', '0000-00-00 00:00:00', 'Hier können Sie uns noch etwas mitteilen.', 0, 'EUR', 1, 'ORDERFOLDER_NEW', '', '', '', '0000-00-00 00:00:00', 0, '', 'OK', 0, 0, 'oxidstandard')";

        $sShopId = $this->getConfig()->GetBaseShopId();
        foreach ($aUserPaymentId as $iCnt => $sUserPaymentId) {
            $sOrderId = "_test" . (time() + $iCnt);
            $sOrderDate = "2011-03-1{$iCnt} 10:55:13";

            $oDb->execute($sQ, array($sOrderId, $sShopId, $sUserId, $sOrderDate, $iCnt + 1, $sUserPaymentId));
        }
    }

    /**
     * Testing get user payment by payment id
     */
    public function testGetPaymentByPaymentType()
    {
        // inserting few test orders
        $this->_insertTestOrders(array('_testOxId5', '_testOxId4', '_testOxId3', '_testOxId2', '_testOxId'), '_testUserId');

        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');

        $oUserPayment = oxNew('oxUserPayment');

        $this->assertTrue($oUserPayment->getPaymentByPaymentType($oUser, 'oxidinvoice'));
        $this->assertEquals('_testOxId', $oUserPayment->getId());
    }

    /**
     * Testing get user payment by payment id - without user
     */
    public function testGetPaymentByPaymentTypeWithoutUserId()
    {
        $oUserPayment = oxNew('oxUserPayment');

        $this->assertFalse($oUserPayment->getPaymentByPaymentType(null, 'oxidcashondel'));
    }

    /**
     * Testing get user payment by payment id - with wrong payment id
     */
    public function testGetPaymentByPaymentTypeWithWrongPaymentType()
    {
        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');

        $oUserPayment = oxNew('oxUserPayment');

        $this->assertFalse($oUserPayment->getPaymentByPaymentType($oUser, 'nosuchpaymentid'));
    }

    public function testGetPaymentByPaymentTypePaymentIdIsNull()
    {
        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');

        $oUserPayment = oxNew('oxUserPayment');

        $this->assertFalse($oUserPayment->getPaymentByPaymentType($oUser, null));
    }

    public function testGetPaymentByPaymentTypeUserAndPaymentIdIsNull()
    {
        $oUserPayment = oxNew('oxUserPayment');
        $this->assertFalse($oUserPayment->getPaymentByPaymentType(null, null));
    }

    /**
     * Testing dyn values getter
     */
    public function testGetDynValues()
    {
        $sDyn = 'kktype__visa@@kknumber__12345@@kkmonth__11@@kkyear__2008@@kkname__testName@@kkpruef__56789@@';
        $aDynVal = oxRegistry::getUtils()->assignValuesFromText($sDyn);
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->oxuserpayments__oxvalue = new oxField($sDyn, oxField::T_RAW);
        $this->assertEquals($aDynVal, $oUserPayment->getDynValues());
    }

    /**
     * Testing dyn values getter
     */
    public function testGetDynValuesNotAllowed()
    {
        $sDyn = 'kktype__visa@@kknumber__12345@@kkmonth__11@@kkyear__2008@@kkname__testName@@kkpruef__56789@@';
        $oUserPayment = oxNew('oxUserPayment');
        $oUserPayment->setStoreCreditCardInfo(false);
        $oUserPayment->oxuserpayments__oxvalue = new oxField($sDyn, oxField::T_RAW);
        $oUserPayment->oxuserpayments__oxpaymentsid = new oxField('oxidcreditcard', oxField::T_RAW);
        $this->assertNull($oUserPayment->getDynValues());
    }
}
