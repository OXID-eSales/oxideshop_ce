<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing user class
 */
class UserTest extends \OxidTestCase
{
    protected $_oUser = array();

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        // setting up user
        $this->setupUsers();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
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
        $sTable = getViewName('oxuser');
        $iLastCustNr = ( int ) $myDB->getOne('select max( oxcustnr ) from ' . $sTable) + 1;
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
        $this->getConfig()->setConfigParam('aMustFillFields', array("bb" => "aa"));
        $oUserView = oxNew('user');
        $this->assertEquals(array("aa" => "bb"), $oUserView->getMustFillFields());
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
        $this->assertEquals(1, $oUserView->getLoginOption());
    }

    public function testGetLoginOptionIfNotLogedIn()
    {
        $this->setRequestParameter('option', 2);
        $oUserView = oxNew('user');
        $this->assertEquals(0, $oUserView->getLoginOption());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was't submited
     */
    public function testGetOrderRemarkNoRemark()
    {
        // get user returns false (not logged in)
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(false));

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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('test', $oUserView->getOrderRemark());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was submited
     */
    public function testGetOrderRemarkFromSession()
    {
        // setting the variable
        $this->getSession()->setVariable('ordrem', "test");
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(true));
        $this->assertEquals('test', $oUserView->getOrderRemark());
    }

    public function testIsNewsSubscribed()
    {
        $this->setRequestParameter('blnewssubscribed', null);
        $oUserView = oxNew('user');
        $this->assertFalse($oUserView->isNewsSubscribed());
    }

    public function testIsNewsSubscribedIfUserIsLogedIn()
    {
        $oNewsSubscribed = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, array('getOptInStatus'));
        $oNewsSubscribed->expects($this->once())->method('getOptInStatus')->will($this->returnValue(true));
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getNewsSubscription'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oNewsSubscribed));
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
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
     *
     * @return null
     */
    public function testRender()
    {
        $oUserView = oxNew('User');
        $this->assertEquals('page/checkout/user.tpl', $oUserView->render());
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oU = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oU->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oU->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->throwException(new Exception("call is ok")));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oU = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oU->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        try {
            $oU->render();
        } catch (Exception $e) {
            $this->assertEquals('call is ok', $e->getMessage());

            return;
        }
        $this->fail("exception should have been thrown");
    }

    public function testRenderReturnsToBasketIfReservationOnAndBasketEmpty()
    {
        oxTestModules::addFunction('oxutils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new Exception($url);}');

        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->setRequestParameter('sslredirect', 'forced');

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getProductsCount'));
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(0));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations', 'getBasket'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        try {
            $oO->render();
        } catch (Exception $e) {
            $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=basket', $e->getMessage());

            return;
        }
        $this->fail("no Exception thrown in redirect");
    }

    public function testIsDownloadableProductWarning()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('hasDownloadableProducts'));
        $oB->expects($this->once())->method('hasDownloadableProducts')->will($this->returnValue(true));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertTrue($oO->isDownloadableProductWarning());
    }

    public function testISDownloadableProductWarningFalse()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue(false));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertFalse($oO->isDownloadableProductWarning());
    }

    public function testIsDownloadableProductWarningFeatureOff()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("blEnableDownloads", false);

        $oB = oxNew('oxBasket');

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\UserController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertFalse($oO->isDownloadableProductWarning());
    }

    /**
     * Testing user::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oUser = oxNew('User');
        $aResult = array();
        $aResults = array();

        $aResult["title"] = oxRegistry::getLang()->translateString('ADDRESS', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oUser->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oUser->getBreadCrumb());
    }

    /**
     * Testing user::modifyBillAddress()
     *
     * @return null
     */
    public function testModifyBillAddress()
    {
        $this->setConfigParam('blnewssubscribed', true);

        $oUser = oxNew('User');
        $this->assertEquals($this->getRequestParameter('blnewssubscribed'), $oUser->modifyBillAddress());
    }
}
