<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxPayment;
use \oxField;
use \Exception;
use \oxException;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class PaymentHelper2 extends oxPayment
{
    public static $dBasketPrice;

    public function isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        self::$dBasketPrice = $dBasketPrice;

        return true;
    }
}


class PaymentTest extends \OxidTestCase
{
    public function setup(): void
    {
        parent::setUp();
        PaymentHelper2::$dBasketPrice = null;
    }

    /**
     * Tear down the fixture.
     */
    public function tearDown(): void
    {
        $sDelete = "Delete from oxuserpayments where oxuserid = '_testOxId'";
        oxDb::getDb()->Execute($sDelete);

        $this->cleanUpTable('oxuserpayments');
        $this->cleanUpTable('oxorder');

        parent::tearDown();
    }

    /**
     * Test payment view getters and setters
     */
    public function testGetPaymentList()
    {
        oxTestModules::addFunction('oxarticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
        $mySession = oxRegistry::getSession();
        $this->setRequestParameter('sShipSet', 'oxidstandard');
        $this->setConfigParam("blVariantParentBuyable", 1);
        /**
         * Preparing input
         */

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $this->setRequestParameter('deladrid', null);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $this->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->addToBasket('1127', 1);
        $oBasket->calculateBasket();

        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        $mySession->setBasket($oBasket);
        //$this->getSession()->setVariable( 'basket', $oBasket );

        $oPayment = oxNew('Payment');
        $oPayment->setUser($oUser);

        $oPaymentList = $oPayment->getPaymentList();

        $this->assertEquals(3, count($oPaymentList));
    }

    /**
     * Test payment view getters and setters
     */
    public function testGetPaymentCnt()
    {
        oxTestModules::addFunction('oxarticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
        $this->setRequestParameter('sShipSet', 'oxidstandard');
        $this->setConfigParam("blVariantParentBuyable", 1);
        $mySession = oxRegistry::getSession();
        /**
         * Preparing input
         */

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $this->setRequestParameter('deladrid', null);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $this->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->addToBasket('1127', 1);
        $oBasket->calculateBasket();

        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession->setBasket($oBasket);

        $oPayment = oxNew('Payment');
        $oPayment->setUser($oUser);

        $iCnt = $oPayment->getPaymentCnt();

        $this->assertEquals(3, $iCnt);
    }

    public function testGetAllSets()
    {
        oxTestModules::addFunction('oxarticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
        $this->setRequestParameter('sShipSet', 'oxidstandard');
        $this->setConfigParam("blVariantParentBuyable", 1);
        $mySession = oxRegistry::getSession();

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $this->setRequestParameter('deladrid', null);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $this->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->addToBasket('1127', 1);
        $oBasket->calculateBasket();

        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession->setBasket($oBasket);

        $oPayment = oxNew('Payment');
        $oPayment->setUser($oUser);

        $aAllSets = $oPayment->getAllSets();
        $aResultSets = array_keys($aAllSets);
        $aSetsIds = ['oxidstandard'];

        $this->assertEquals($aSetsIds, $aResultSets);
    }

    public function testGetAllSetsCnt()
    {
        oxTestModules::addFunction('oxarticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
        $this->setRequestParameter('sShipSet', 'oxidstandard');
        $this->setConfigParam("blVariantParentBuyable", 1);
        $mySession = oxRegistry::getSession();

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $this->setRequestParameter('deladrid', null);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $this->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->addToBasket('1127', 1);
        $oBasket->calculateBasket();

        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession->setBasket($oBasket);

        $oPayment = oxNew('Payment');
        $oPayment->setUser($oUser);

        $iCnt = $oPayment->getAllSetsCnt();

        $this->assertEquals(1, $iCnt);
    }

    public function testGetEmptyPayment()
    {
        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blOtherCountryOrder', true);

        $oPayment = oxNew('Payment');
        $oPayment->setDefaultEmptyPayment();

        $oEmptyPayment = $oPayment->getEmptyPayment();

        $this->assertEquals('oxempty', $oEmptyPayment->getId());
    }

    public function testGetPaymentErrorWithoutOtherCountryOrder()
    {
        //basket name in session will be "basket"
        $this->setConfigParam('blOtherCountryOrder', false);

        $oPayment = oxNew('Payment');
        $oPayment->setDefaultEmptyPayment();

        $this->assertEquals(-2, $oPayment->getPaymentError());
    }

    public function testGetPaymentErrorFromSession()
    {
        $this->setSessionParam('payerror', 'test');

        $oPayment = oxNew('Payment');
        $oPayment->unsetPaymentErrors();
        $oPayment->getPaymentError();

        $this->assertEquals('test', $oPayment->getPaymentError());
    }

    public function testGetPaymentErrorTextFromSession()
    {
        $this->setSessionParam('payerrortext', 'test');

        $oPayment = oxNew('Payment');
        $oPayment->unsetPaymentErrors();
        $oPayment->getPaymentErrorText();

        $this->assertEquals('test', $oPayment->getPaymentErrorText());
    }

    public function testGetPaymentErrorFromRequest()
    {
        $this->setRequestParameter('payerror', 'test');

        $oPayment = oxNew('Payment');
        $oPayment->unsetPaymentErrors();
        $oPayment->getPaymentError();

        $this->assertEquals('test', $oPayment->getPaymentError());
    }

    public function testGetPaymentErrorTextFromRequest()
    {
        $this->setRequestParameter('payerrortext', 'test');

        $oPayment = oxNew('Payment');
        $oPayment->unsetPaymentErrors();
        $oPayment->getPaymentErrorText();

        $this->assertEquals('test', $oPayment->getPaymentErrorText());
    }

    public function testIsOldDebitValidationEnabled_configSkipNotExist_enabled()
    {
        $oPayment = oxNew('Payment');
        $this->assertTrue($oPayment->isOldDebitValidationEnabled(), 'Old validation should be enabled as no config is set.');
    }

    public function testIsOldDebitValidationEnabled_configSkipDisabled_enabled()
    {
        $this->getConfig()->setConfigParam('blSkipDebitOldBankInfo', false);
        $oPayment = oxNew('Payment');
        $this->assertTrue($oPayment->isOldDebitValidationEnabled(), 'Old validation should be enabled as it is set in config.');
    }

    public function testIsOldDebitValidationEnabled_configSkipEnabled_disabled()
    {
        $this->getConfig()->setConfigParam('blSkipDebitOldBankInfo', true);
        $oPayment = oxNew('Payment');
        $this->assertFalse($oPayment->isOldDebitValidationEnabled(), 'Old validation should be disabled as it is set in config.');
    }

    public function testGetDynValueSetInSession()
    {
        $this->setSessionParam('usr', 'oxdefaultadmin');
        $this->setSessionParam('dynvalue', 'test');
        $this->setRequestParameter('dynvalue', 'test2');

        $oPayment = oxNew('Payment');
        $this->assertEquals('test', $oPayment->getDynValue());
    }

    public function testGetDynValueNotSetInSession()
    {
        $this->setSessionParam('usr', 'oxdefaultadmin');
        $this->setSessionParam('dynvalue', null);
        $this->setRequestParameter('dynvalue', 'test2');

        $oPayment = oxNew('Payment');
        $this->assertEquals('test2', $oPayment->getDynValue());
    }

    /**
     * Inserting test orders
     */
    protected function insertTestOrders($aUserPaymentId, $sUserId)
    {
        $oDb = oxDb::getDb();

        $sQ = "INSERT INTO `oxorder`
                   (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLCOMPANY`, `OXBILLEMAIL`, `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLADDINFO`, `OXBILLUSTID`, `OXBILLCITY`, `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLFAX`, `OXBILLSAL`, `OXDELCOMPANY`, `OXDELFNAME`, `OXDELLNAME`, `OXDELSTREET`, `OXDELSTREETNR`, `OXDELADDINFO`, `OXDELCITY`, `OXDELCOUNTRYID`, `OXDELSTATEID`, `OXDELZIP`, `OXDELFON`, `OXDELFAX`, `OXDELSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXARTVAT2`, `OXARTVATPRICE2`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXCARDID`, `OXCARDTEXT`, `OXDISCOUNT`, `OXEXPORT`, `OXBILLNR`, `OXTRACKCODE`, `OXSENDDATE`, `OXREMARK`, `OXVOUCHERDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`, `OXTRANSID`, `OXPAYID`, `OXXID`, `OXPAID`, `OXSTORNO`, `OXIP`, `OXTRANSSTATUS`, `OXLANG`, `OXINVOICENR`, `OXDELTYPE`)
               VALUES
                   (?, ?, ?, ?, ?, '', 'user@oxid-esales.com', 'Marc', 'Muster', 'Hauptstr.', '13', '', '', 'Freiburg', 'a7c40f631fc920687.20179984', 'BW', '79098', '', '', 'MR', '', '', '', '', '', '', '', '', '', '', '', '', '', ?, 'oxiddebitnote', 1639.15, 2108.39, 1950.59, 19, 311.44, 0, 0, 0, 19, 0, 0, 0, 0, '', '', 157.8, 0, '', '', '0000-00-00 00:00:00', 'Hier k�nnen Sie uns noch etwas mitteilen.', 0, 'EUR', 1, 'ORDERFOLDER_NEW', '', '', '', '0000-00-00 00:00:00', 0, '', 'OK', 0, 0, 'oxidstandard')";

        $sShopId = $this->getConfig()->GetBaseShopId();
        foreach ($aUserPaymentId as $iCnt => $sUserPaymentId) {
            $sOrderId = "_test" . (time() + $iCnt);
            $sOrderDate = sprintf('2011-03-1%s 10:55:13', $iCnt);

            $oDb->execute($sQ, [$sOrderId, $sShopId, $sUserId, $sOrderDate, $iCnt + 1, $sUserPaymentId]);
        }
    }

    public function testGetDynValueIfDebitNoteIsSet()
    {
        $this->insertTestOrders(['_testOxId3', '_testOxId2', '_testOxId'], "oxdefaultadmin");
        $this->setSessionParam('dynvalue', ['lsbankname' => '']);

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oUpay = oxNew('oxuserpayment');
        $oUpay->setId('_testOxId');

        $oUpay->oxuserpayments__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $oUpay->oxuserpayments__oxvalue = new oxField('lsbankname__test@@', oxField::T_RAW);
        $oUpay->oxuserpayments__oxpaymentsid = new oxField('oxiddebitnote', oxField::T_RAW);
        $oUpay->save();

        $oPayment = $this->getMock(\OxidEsales\Eshop\Application\Controller\PaymentController::class, ["getPaymentList"]);
        $oPayment->expects($this->once())->method('getPaymentList')->will($this->returnValue(['oxiddebitnote' => true]));
        $oPayment->setUser($oUser);
        $this->assertEquals(['lsbankname' => 'test'], $oPayment->getDynValue());
    }

    public function testGetCheckedPaymentId()
    {
        $this->setRequestParameter('paymentid', 'testId');

        $oPayment = $this->getProxyClass("payment");
        $oPaymentList = [];
        $oPaymentList['testId'] = true;
        $oPayment->setNonPublicVar("_oPaymentList", $oPaymentList);

        $this->assertEquals('testId', $oPayment->getCheckedPaymentId());
    }

    public function testGetCheckedPaymentIdSelectedPayment()
    {
        $this->setRequestParameter('paymentid', null);
        $this->setSessionParam('_selected_paymentid', 'testId2');

        $oPayment = $this->getProxyClass("payment");
        $oPaymentList = [];
        $oPaymentList['testId2'] = true;
        $oPayment->setNonPublicVar("_oPaymentList", $oPaymentList);

        $this->assertEquals('testId2', $oPayment->getCheckedPaymentId());
    }

    public function testGetCheckedPaymentIdLastUserPaymentType()
    {
        $this->setRequestParameter('paymentid', null);
        $this->setSessionParam('_selected_paymentid', null);

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        oxTestModules::addFunction('oxorder', 'getLastUserPaymentType', '{return "testId3";}');
        $oPayment = $this->getProxyClass("payment");
        $oPayment->setUser($oUser);

        $oPaymentList = [];
        $oPaymentList['testId3'] = true;
        $oPayment->setNonPublicVar("_oPaymentList", $oPaymentList);

        $this->assertEquals('testId3', $oPayment->getCheckedPaymentId());
    }

    public function testGetCheckedPaymentIdNotAllowed()
    {
        $this->setRequestParameter('paymentid', null);
        $this->setSessionParam('_selected_paymentid', 'testId4');

        $oPayment = $this->getProxyClass("payment");
        $oPaymentList = [];
        $oPaymentList['testId3'] = true;
        $oPayment->setNonPublicVar("_oPaymentList", $oPaymentList);

        $this->assertEquals('testId3', $oPayment->getCheckedPaymentId());
    }

    public function testGetCheckedPaymentIdInDB()
    {
        $this->setRequestParameter('paymentid', null);
        $this->setSessionParam('_selected_paymentid', null);
        $oPayment = $this->getProxyClass("payment");
        $oPaymentList = [];
        $oPaymentList['testId'] = true;
        $oPayment->setNonPublicVar("_sCheckedId", 'testId');
        $oPayment->setNonPublicVar("_oPaymentList", $oPaymentList);

        $this->assertEquals('testId', $oPayment->getCheckedPaymentId());
    }

    /*
     * Check if payment validation uses basket price getted from oxBasket::getPriceForPayment()
     * (M:1145)
     */
    public function testValidatePayment_userBasketPriceForPayment()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\PaymentHelper2::class, 'oxPayment');

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getPriceForPayment']);
        $oBasket->expects($this->atLeastOnce())->method('getPriceForPayment')->will($this->returnValue(100));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oPayment = $this->getMock(\OxidEsales\Eshop\Application\Controller\PaymentController::class, ['getUser']);
        $oPayment->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
        $this->setRequestParameter("paymentid", 'testId');

        $oPayment->validatePayment();

        $this->assertEquals(100, PaymentHelper2::$dBasketPrice);
    }

    /**
     * #M1432: Error message by shipping options - Frontend
     */
    public function testValidatePaymentDifferentShipping()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\PaymentHelper2::class, 'oxPayment');

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getPriceForPayment']);
        $oBasket->expects($this->atLeastOnce())->method('getPriceForPayment')->will($this->returnValue(100));
        $oBasket->setShipping('currentShipping');

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oPayment = $this->getMock(\OxidEsales\Eshop\Application\Controller\PaymentController::class, ['getUser']);
        $oPayment->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
        $this->setRequestParameter("paymentid", 'testId');
        $this->setRequestParameter("sShipSet", 'newShipping');

        $oPayment->validatePayment();

        $this->assertEquals('newShipping', $oBasket->getShippingId());
    }

    protected function checkInArrayRecursive($needle, $haystack)
    {
        return in_array($needle, $haystack);
    }

    public function testRenderDoesNotCleanReservationsIfOff()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{throw new Exception("REDIRECT");}');
        $this->setConfigParam('blPsBasketReservationEnabled', false);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->never())->method('getBasketReservations');

        $oP = oxNew(\OxidEsales\Eshop\Application\Controller\PaymentController::class);

        try {
            $oP->render();
        } catch (Exception $exception) {
            $this->assertEquals("REDIRECT", $exception->getMessage());
        }
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{throw new Exception("REDIRECT");}');
        $this->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', ['renewExpiration']);
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oP = oxNew(\OxidEsales\Eshop\Application\Controller\PaymentController::class);

        try {
            $oP->render();
        } catch (Exception $exception) {
            $this->assertEquals("REDIRECT", $exception->getMessage());
        }
    }

    public function testRenderReturnsToBasketIfReservationOnAndBasketEmpty()
    {
        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new Exception($url);}');
        $this->setConfigParam('blPsBasketReservationEnabled', true);
        $this->setRequestParameter('sslredirect', 'forced');

        $oR = $this->getMock('stdclass', ['renewExpiration']);
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getProductsCount']);
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(0));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        $session->expects($this->any())->method('getBasket')->will($this->returnValue($oB));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = oxNew(\OxidEsales\Eshop\Application\Controller\PaymentController::class);

        try {
            $oO->render();
        } catch (Exception $exception) {
            $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=basket', $exception->getMessage());

            return;
        }

        $this->fail("no Exception thrown in redirect");
    }

    public function testRenderNoUserWithBasket()
    {
        $sRedirUrl = $this->getConfig()->getShopHomeURL() . 'cl=basket';
        $this->expectException('oxException');
        $this->expectExceptionMessage($sRedirUrl);

        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new oxException($url);}');
        $this->setConfigParam('blPsBasketReservationEnabled', false);
        // skip redirect to SSL
        $this->setRequestParameter('sslredirect', 'forced');

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getProductsCount']);
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->any())->method('getBasket')->will($this->returnValue($oB));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\PaymentController::class, ['getUser']);
        $oO->expects($this->any())->method('getUser')->will($this->returnValue(null));
        $oO->render();
    }

    public function testRenderNoUserEmptyBasket()
    {
        $sRedirUrl = $this->getConfig()->getShopHomeURL() . 'cl=start';
        $this->expectException('oxException');
        $this->expectExceptionMessage($sRedirUrl);

        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new oxException($url);}');
        $this->setConfigParam('blPsBasketReservationEnabled', false);
        // skip redirect to SSL
        $this->setRequestParameter('sslredirect', 'forced');

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getProductsCount']);
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(0));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->any())->method('getBasket')->will($this->returnValue($oB));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\PaymentController::class, ['getUser']);
        $oO->expects($this->any())->method('getUser')->will($this->returnValue(null));
        $oO->render();
    }

    /**
     * Testing Payment::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oPayment = oxNew('Payment');

        $this->assertEquals(1, count($oPayment->getBreadCrumb()));
    }

    /**
     * Testing Payment::_CheckArrValuesEmpty() when checked array is empty
     */
    public function test_CheckArrValuesEmptyWithoutData()
    {
        $oPayment = $this->getProxyClass("payment");
        $aData = ["kktype" => null, "kknumber" => null];
        $aKeys = ["kktype", "kknumber"];

        $this->assertTrue($oPayment->checkArrValuesEmpty($aData, $aKeys));
    }

    /**
     * Testing Payment::_CheckArrValuesEmpty() when checked array is populated with good data
     */
    public function test_CheckArrValuesEmptyWithData()
    {
        $oPayment = $this->getProxyClass("payment");
        $aData = ["kktype" => "vis", "kknumber" => "42222222"];
        $aKeys = ["kktype", "kknumber"];

        $this->assertFalse($oPayment->checkArrValuesEmpty($aData, $aKeys));
    }

    /**
     * Testing Payment::_CheckArrValuesEmpty() when checked array is populated with bad data
     */
    public function test_CheckArrValuesEmptyWithBadData()
    {
        $oPayment = $this->getProxyClass("payment");
        $aKeys = ["kktype", "kknumber"];

        $this->assertTrue($oPayment->checkArrValuesEmpty(null, $aKeys));
    }

    /**
     * Test payment if changed shipping
     */
    public function testChangeshippingt()
    {
        $this->setRequestParameter('sShipSet', 'paypal');
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['onUpdate', 'setShipping']);
        $oBasket->expects($this->once())->method('onUpdate');
        $oBasket->expects($this->once())->method('setShipping')->with($this->equalTo(null));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oPayment = oxNew(\OxidEsales\Eshop\Application\Controller\PaymentController::class);
        $oPayment->changeshipping();

        $this->assertEquals('paypal', $this->getSessionParam('sShipSet'));
    }

    public function testIsPaymentVatSplitted()
    {
        $this->setConfigParam('blShowVATForPayCharge', true);

        $oPayment = oxNew('Payment');
        $this->assertTrue($oPayment->isPaymentVatSplitted());
    }
}
