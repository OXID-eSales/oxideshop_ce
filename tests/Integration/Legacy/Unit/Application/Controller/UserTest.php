<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \Exception;
use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing user class
 */
class UserTest extends \PHPUnit\Framework\TestCase
{
    protected $_oUser = [];

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // setting up user
        $this->setupUsers();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        if ($this->_oUser) {
            $this->_oUser->delete();
        }

        parent::tearDown();
    }

    /**
     * Setting up users
     */
    protected function setupUsers()
    {
        $myDB = oxDb::getDB();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxuser');
        $iLastCustNr = (int) $myDB->getOne('select max( oxcustnr ) from ' . $sTable) + 1;
        $this->_oUser = oxNew('oxuser');
        $this->_oUser->oxuser__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->_oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $this->_oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $this->_oUser->oxuser__oxusername = new oxField('test@oxid-esales.com', oxField::T_RAW);
        $this->_oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'), oxField::T_RAW);
        $this->_oUser->oxuser__oxcustnr = new oxField($iLastCustNr + 1, oxField::T_RAW);
        $this->_oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $this->_oUser->save();

        $sQ = 'insert into oxaddress ( oxid, oxuserid, oxaddressuserid, oxcountryid ) values ( "test_user", "' . $this->_oUser->getId() . '", "' . $this->_oUser->getId() . '", "testCountry" ) ';
        $myDB->Execute($sQ);
    }

    public function testGetMustFillFields()
    {
        $this->getConfig()->setConfigParam('aMustFillFields', ["bb" => "aa"]);
        $oUserView = oxNew('user');
        $this->assertSame(["aa" => "bb"], $oUserView->getMustFillFields());
    }

    public function testGetShowNoRegOption()
    {
        $this->getConfig()->setConfigParam('blOrderDisWithoutReg', true);
        $oUserView = oxNew('user');
        $this->assertFalse($oUserView->getShowNoRegOption());
    }

    public function testGetLoginOption()
    {
        $this->setRequestParameter('option', 1);
        $oUserView = oxNew('user');
        $this->assertSame(1, $oUserView->getLoginOption());
    }

    public function testGetLoginOptionIfNotLogedIn()
    {
        $this->setRequestParameter('option', 2);
        $oUserView = oxNew('user');
        $this->assertSame(0, $oUserView->getLoginOption());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was't submited
     */
    public function testGetOrderRemarkNoRemark()
    {
        // get user returns false (not logged in)
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, ['getUser']);
        $oUserView->expects($this->once())->method('getUser')->willReturn(false);

        // not connected and no post (will return false)
        $this->assertFalse($oUserView->getOrderRemark());
    }

    /**
     * Tests User::getOrderRemark() when logged in
     */
    public function testGetOrderRemarkFromPost()
    {
        // gettin order remark from post (when not logged in)
        $this->setRequestParameter('order_remark', 'test');

        // get user returns false (not logged in)
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, ['getUser']);
        $oUserView->expects($this->once())->method('getUser')->willReturn(false);
        $this->assertSame('test', $oUserView->getOrderRemark());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was submited
     */
    public function testGetOrderRemarkFromSession()
    {
        // setting the variable
        $this->getSession()->setVariable('ordrem', "test");
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, ['getUser']);
        $oUserView->expects($this->once())->method('getUser')->willReturn(true);
        $this->assertSame('test', $oUserView->getOrderRemark());
    }

    public function testIsNewsSubscribed()
    {
        $this->setRequestParameter('blnewssubscribed', null);
        $oUserView = oxNew('user');
        $this->assertFalse($oUserView->isNewsSubscribed());
    }

    public function testIsNewsSubscribedIfUserIsLogedIn()
    {
        $oNewsSubscribed = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, ['getOptInStatus']);
        $oNewsSubscribed->expects($this->once())->method('getOptInStatus')->willReturn(true);
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getNewsSubscription']);
        $oUser->expects($this->once())->method('getNewsSubscription')->willReturn($oNewsSubscribed);
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, ['getUser']);
        $oUserView->expects($this->once())->method('getUser')->willReturn($oUser);
        $this->assertTrue($oUserView->isNewsSubscribed());
    }

    public function testShowShipAddress()
    {
        $oUserView = oxNew('user');

        $this->getSession()->setVariable('blshowshipaddress', false);
        $this->assertFalse($oUserView->showShipAddress());

        $this->getSession()->setVariable('blshowshipaddress', true);
        $this->assertTrue($oUserView->showShipAddress());
    }

    /**
     * Test view render.
     */
    public function testRender()
    {
        $oUserView = oxNew('User');
        $this->assertSame('page/checkout/user', $oUserView->render());
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->never())->method('getBasketReservations');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oU = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);
        $oU->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', ['renewExpiration']);
        $oR->expects($this->once())->method('renewExpiration')->willThrowException(new Exception("call is ok"));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->once())->method('getBasketReservations')->willReturn($oR);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oU = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);

        try {
            $oU->render();
        } catch (Exception $exception) {
            $this->assertSame('call is ok', $exception->getMessage());

            return;
        }

        $this->fail("exception should have been thrown");
    }

    public function testRenderReturnsToBasketIfReservationOnAndBasketEmpty()
    {
        oxTestModules::addFunction('oxutils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new Exception($url);}');

        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->setRequestParameter('sslredirect', 'forced');

        $oR = $this->getMock('stdclass', ['renewExpiration']);
        $oR->expects($this->once())->method('renewExpiration')->willReturn(null);

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getProductsCount']);
        $oB->expects($this->once())->method('getProductsCount')->willReturn(0);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->once())->method('getBasketReservations')->willReturn($oR);
        $session->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);

        try {
            $oO->render();
        } catch (Exception $exception) {
            $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=basket', $exception->getMessage());

            return;
        }

        $this->fail("no Exception thrown in redirect");
    }

    public function testIsDownloadableProductWarning()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['hasDownloadableProducts']);
        $oB->expects($this->once())->method('hasDownloadableProducts')->willReturn(true);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);
        $this->assertTrue($oO->isDownloadableProductWarning());
    }

    public function testISDownloadableProductWarningFalse()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->method('getBasket')->willReturn(false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);
        $this->assertFalse($oO->isDownloadableProductWarning());
    }

    public function testIsDownloadableProductWarningFeatureOff()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", false);

        $oB = oxNew('oxBasket');

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oO = oxNew(\OxidEsales\Eshop\Application\Controller\UserController::class);
        $this->assertFalse($oO->isDownloadableProductWarning());
    }

    /**
     * Testing user::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oUser = oxNew('User');
        $aResult = [];
        $aResults = [];

        $aResult["title"] = oxRegistry::getLang()->translateString('ADDRESS', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oUser->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oUser->getBreadCrumb());
    }

    /**
     * Testing user::modifyBillAddress()
     */
    public function testModifyBillAddress()
    {
        $this->setConfigParam('blnewssubscribed', true);

        $oUser = oxNew('User');
        $this->assertEquals($this->getRequestParameter('blnewssubscribed'), $oUser->modifyBillAddress());
    }
}
