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
 * Testing user class
 */
class Unit_Views_userTest extends OxidTestCase
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
        $this->_oUser->oxuser__oxshopid = new oxField(modConfig::getInstance()->getShopId(), oxField::T_RAW);
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
        modConfig::getInstance()->setConfigParam('aMustFillFields', array("bb" => "aa"));
        $oUserView = new user();
        $this->assertEquals(array("aa" => "bb"), $oUserView->getMustFillFields());
    }

    public function testGetShowNoRegOption()
    {
        modConfig::getInstance()->setConfigParam('blOrderDisWithoutReg', true);
        $oUserView = new user();
        $this->assertFalse($oUserView->getShowNoRegOption());
    }

    public function testGetLoginOption()
    {
        modConfig::setRequestParameter('option', 1);
        $oUserView = new user();
        $this->assertEquals(1, $oUserView->getLoginOption());
    }

    public function testGetLoginOptionIfNotLogedIn()
    {
        modConfig::setRequestParameter('option', 2);
        $oUserView = new user();
        $this->assertEquals(0, $oUserView->getLoginOption());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was't submited
     */
    public function testGetOrderRemarkNoRemark()
    {
        // get user returns false (not logged in)
        $oUserView = $this->getMock('user', array('getUser'));
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
        modConfig::setRequestParameter('order_remark', 'test');

        // get user returns false (not logged in)
        $oUserView = $this->getMock('user', array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('test', $oUserView->getOrderRemark());
    }

    /**
     * Tests User::getOrderRemark() when not logged in and form was submited
     */
    public function testGetOrderRemarkFromSession()
    {
        // setting the variable
        modSession::getInstance()->setVar('ordrem', "test");
        $oUserView = $this->getMock('user', array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(true));
        $this->assertEquals('test', $oUserView->getOrderRemark());
    }

    public function testIsNewsSubscribed()
    {
        modConfig::setRequestParameter('blnewssubscribed', null);
        $oUserView = new user();
        $this->assertFalse($oUserView->isNewsSubscribed());
    }

    public function testIsNewsSubscribedIfUserIsLogedIn()
    {
        $oNewsSubscribed = $this->getMock('oxNewsSubscribed', array('getOptInStatus'));
        $oNewsSubscribed->expects($this->once())->method('getOptInStatus')->will($this->returnValue(true));
        $oUser = $this->getMock('oxuser', array('getNewsSubscription'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oNewsSubscribed));
        $oUserView = $this->getMock('user', array('getUser'));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oUserView->isNewsSubscribed());
    }

    public function testShowShipAddress()
    {
        $oUserView = new user();

        modSession::getInstance()->setVar('blshowshipaddress', false);
        $this->assertFalse($oUserView->showShipAddress());

        modSession::getInstance()->setVar('blshowshipaddress', true);
        $this->assertTrue($oUserView->showShipAddress());

    }

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $oUserView = new User();
        $this->assertEquals('page/checkout/user.tpl', $oUserView->render());
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oU = $this->getMock('user', array('getSession'));
        $oU->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oU->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->evalFunction('{throw new Exception("call is ok");}'));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oU = $this->getMock('user', array('getSession'));
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
        modInstances::addMod('oxutils', oxNew('oxutils'));

        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', true);
        modConfig::setRequestParameter('sslredirect', 'forced');

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oB = $this->getMock('oxbasket', array('getProductsCount'));
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(0));

        $oS = $this->getMock('oxsession', array('getBasketReservations', 'getBasket'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock('user', array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        try {
            $oO->render();
        } catch (Exception $e) {
            $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=basket', $e->getMessage());

            return;
        }
        $this->fail("no Exception thrown in redirect");
    }

    /**
     * Testing if render calls function for filling user data taken
     * from Facebook account - FB connect is disabled
     *
     * @return null
     */
    public function testRenderFillsFormWithFbUserData_FbConnectDisabled()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("bl_showFbConnect", false);

        $oView = $this->getMock("user", array("_fillFormWithFacebookData"));
        $oView->expects($this->never())->method('_fillFormWithFacebookData');
        $oView->render();
    }

    /**
     * Testing if render calls function for filling user data taken
     * from Facebook account - FB connect is enabled and no user
     *
     * @return null
     */
    public function testRenderFillsFormWithFbUserData_FbConnectEnabledNoUser()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("bl_showFbConnect", true);

        $oView = $this->getMock("user", array("_fillFormWithFacebookData", "getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(null));
        $oView->expects($this->once())->method('_fillFormWithFacebookData');
        $oView->render();
    }

    /**
     * Testing if render calls function for filling user data taken
     * from Facebook account - FB connect is enabled
     *
     * @return null
     */
    public function testRenderFillsFormWithFbUserData_FbConnectEnabledUserConnected()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("bl_showFbConnect", true);
        $oUser = new oxUser();

        $oView = $this->getMock("user", array("_fillFormWithFacebookData", "getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->never())->method('_fillFormWithFacebookData');
        $oView->render();
    }

    /**
     * Testing if render calls function for filling user data taken
     * from Facebook account - FB connect is enabled
     *
     * @return null
     */
    public function testFillFormWithFacebookData()
    {
        oxTestModules::addFunction("oxFb", "isConnected", "{return true;}");
        oxTestModules::addFunction("oxFb", "api", "{return array(first_name=>'testFirstName', last_name=>'testLastName');}");

        $oView = $this->getProxyClass("user");
        $oView->UNITfillFormWithFacebookData();

        $aViewData = $oView->getInvoiceAddress();
        $this->assertEquals("testFirstName", $aViewData["oxuser__oxfname"]);
        $this->assertEquals("testLastName", $aViewData["oxuser__oxlname"]);
    }

    /**
     * Testing if render calls function for filling user data taken
     * from Facebook account - data already filled up
     *
     * @return null
     */
    public function testFillFormWithFacebookData_dateAlreadyPrefilled()
    {
        oxTestModules::addFunction("oxFb", "isConnected", "{return true;}");
        oxTestModules::addFunction("oxFb", "api", "{return array(first_name=>'testFirstName', last_name=>'testLastName');}");

        $oView = $this->getProxyClass("user");
        $aViewData["invadr"]["oxuser__oxfname"] = "testValue1";
        $aViewData["invadr"]["oxuser__oxlname"] = "testValue2";
        $aViewData = $oView->setNonPublicVar("_aViewData", $aViewData);

        $oView->UNITfillFormWithFacebookData();

        $aViewData = $oView->getNonPublicVar("_aViewData");
        $this->assertEquals("testValue1", $aViewData["invadr"]["oxuser__oxfname"]);
        $this->assertEquals("testValue2", $aViewData["invadr"]["oxuser__oxlname"]);
    }

    public function testIsDownloadableProductWarning()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $oB = $this->getMock('oxbasket', array('hasDownloadableProducts'));
        $oB->expects($this->once())->method('hasDownloadableProducts')->will($this->returnValue(true));

        $oS = $this->getMock('oxsession', array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock('user', array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertTrue($oO->isDownloadableProductWarning());
    }

    public function testISDownloadableProductWarningFalse()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("blEnableDownloads", true);

        $oS = $this->getMock('oxsession', array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue(false));

        $oO = $this->getMock('user', array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertFalse($oO->isDownloadableProductWarning());
    }

    public function testIsDownloadableProductWarningFeatureOff()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam("blEnableDownloads", false);

        $oB = new oxBasket();

        $oS = $this->getMock('oxsession', array('getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock('user', array('getSession'));
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
        $oUser = new User();
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

        $oUser = new User();
        $this->assertEquals(oxRegistry::getConfig()->getRequestParameter('blnewssubscribed'), $oUser->modifyBillAddress());
    }
}
