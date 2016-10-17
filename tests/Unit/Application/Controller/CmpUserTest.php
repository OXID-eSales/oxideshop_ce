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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller;

use \oxcmp_user;
use \Exception;
use \oxField;
use \oxUserException;
use \oxCookieException;
use \oxInputException;
use \oxConnectionException;
use \oxUser;
use \oxException;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class modcmp_user_parent
{

    public $sModDynUrlParams = '&amp;searchparam=a';

    public function getDynUrlParams()
    {
        return $this->sModDynUrlParams;
    }
}

class modcmp_user extends oxcmp_user
{

    protected $_oParent;

    public function getLogoutLink()
    {
        $this->_oParent = new modcmp_user_parent();

        return $this->_getLogoutLink();
    }

}

class CmpUserTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // cleaning up
        $sQ = 'delete from oxuser where oxusername like "test%" ';
        oxDb::getDb()->execute($sQ);
        $sQ = 'delete from oxaddress where oxid like "test%" ';
        oxDb::getDb()->execute($sQ);

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxnewssubscribed');

        parent::tearDown();
    }

    /**
     * Test view init().
     *
     * @return null
     */
    public function testInitInv()
    {
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        // testing..
        $oView = $this->getMock("oxcmp_user", array("getInvitor"), array(), '', false);
        $oView->expects($this->once())->method('getInvitor');
        $oView->init();
    }

    /**
     * Test view getInvitor().
     *
     * @return null
     */
    public function testGetInvitor()
    {
        // testing..
        $this->setRequestParameter('su', 'testid');
        $oView = oxNew('oxcmp_user');
        $oView->getInvitor();
        $this->assertEquals('testid', $this->getSession()->getVariable('su'));
    }

    /**
     * Test view render().
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');

        $oConfig = $this->getMock("oxConfig", array("getShopHomeURL"));
        $oConfig->expects($this->once())->method('getShopHomeURL')->will($this->returnValue("testUrl"));

        $oParent = $this->getMock("oxUbase", array("getClassName", "isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('getClassName')->will($this->returnValue("test"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        try {
            // testing..
            $oView = $this->getMock("oxcmp_user", array("getUser", "getConfig", "getParent"), array(), '', false);
            $oView->expects($this->once())->method('getUser')->will($this->returnValue(false));
            $oView->expects($this->atLeastOnce())->method('getParent')->will($this->returnValue($oParent));
            $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
            $oView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals("testUrlcl=account", $oExcp->getMessage(), "Error in oxscloginoxcmpuser::render()");

            return;
        }
        $this->fail("Error in oxscloginoxcmpuser::render()");
    }

    /**
     * Test view render().
     *
     * @return null
     */
    public function testRenderRegistration()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $this->setRequestParameter('cl', 'register');

        $oConfig = $this->getMock("oxConfig", array("getShopHomeURL"));
        $oConfig->expects($this->any())->method('getShopHomeURL')->will($this->returnValue("testUrl"));

        $oUser = $this->getMock("oxcmp_user", array("isTermsAccepted"));
        $oUser->expects($this->any())->method('isTermsAccepted')->will($this->throwException(new Exception("isTermsAccepted")));

        $oParent = $this->getMock("oxUbase", array("getClassName", "isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('getClassName')->will($this->returnValue("test"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        // testing..
        $oView = $this->getMock("oxcmp_user", array("getUser", "getConfig", "_checkTermVersion", "getParent"), array(), '', false);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        try {
            $this->assertFalse($oView->render());
        } catch (Exception $oExcp) {
            $this->assertEquals("isTermsAccepted", $oExcp->getMessage(), "Error in testRenderRegistration");

            return;
        }
        $this->fail("Error in testRenderRegistration");
    }

    /**
     * Test view render().
     *
     * @return null
     */
    public function testRenderConfirmTerms()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oConfig = $this->getMock("oxConfig", array("getShopHomeURL", "getConfigParam"));
        $oConfig->expects($this->any())->method('getShopHomeURL')->will($this->returnValue("testUrl"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(true));

        $oUser = $this->getMock("oxcmp_user", array("isTermsAccepted"));
        $oUser->expects($this->any())->method('isTermsAccepted')->will($this->returnValue(false));

        $oParent = $this->getMock("oxUbase", array("getClassName"));
        $oParent->expects($this->once())->method('getClassName')->will($this->returnValue("test"));

        try {
            // testing..
            $oView = $this->getMock("oxcmp_user", array("getUser", "getConfig", "getParent"), array(), '', false);
            $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
            $oView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
            $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
            $oView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals("testUrlcl=account&term=1", $oExcp->getMessage(), "Error in oxscloginoxcmpuser::render()");

            return;
        }
        $this->fail("Error in oxscloginoxcmpuser::render()");
    }

    /**
     * Test view logout().
     *
     * @return null
     */
    public function testLogoutForLoginFeature()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Skip CE/PE related tests for EE edition");
        }

        oxTestModules::addFunction("oxUser", "logout", "{ return true;}");

        $aMockFnc = array('_afterLogout', '_getLogoutLink', 'getParent');

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock('oxcmp_user', $aMockFnc);
        $oUserView->expects($this->once())->method('_afterLogout');
        $oUserView->expects($this->any())->method('_getLogoutLink')->will($this->returnValue("testurl"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));

        $this->assertEquals('account', $oUserView->logout());
    }

    /**
     * Test view login_noredirect().
     *
     * @return null
     */
    public function testLoginNoredirectAlt()
    {
        $this->getConfig()->setConfigParam('blConfirmAGB', true);
        $this->setRequestParameter('ord_agb', true);

        $oUser = $this->getMock("oxUser", array("acceptTerms"));
        $oUser->expects($this->once())->method('acceptTerms');

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock('oxcmp_user', array('login', 'getUser', 'getParent'));
        $oUserView->expects($this->never())->method('login');
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertNull($oUserView->login_noredirect());
    }

    /**
     * Test view createUser().
     *
     * @return null
     */
    public function testCreateUserForLoginFeature()
    {
        oxTestModules::addFunction("oxemail", "sendRegisterEmail", "{ return true;}");
        $this->setRequestParameter('lgn_usr', 'test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd', 'Test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd2', 'Test@oxid-esales.com');
        $this->setRequestParameter('ord_agb', true);
        $this->setRequestParameter('option', 3);
        $aRawVal = array('oxuser__oxfname'     => 'fname',
                         'oxuser__oxlname'     => 'lname',
                         'oxuser__oxstreetnr'  => 'nr',
                         'oxuser__oxstreet'    => 'street',
                         'oxuser__oxzip'       => 'zip',
                         'oxuser__oxcity'      => 'city',
                         'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');
        $this->setRequestParameter('invadr', $aRawVal);

        $this->getConfig()->setConfigParam("blInvitationsEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('login', 'getParent'));
        $oUserView->expects($this->any())->method('login')->will($this->returnValue('payment'));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));

        $this->assertEquals('payment?new_user=1&success=1', $oUserView->createUser());
        $this->assertTrue($oUserView->getNonPublicVar('_blIsNewUser'));
    }

    /**
     * Test view createUser().
     *
     * @return null
     */
    public function testCreateUserForInvitationFeature()
    {
        oxTestModules::addFunction("oxuser", "checkValues", "{ return true;}");
        oxTestModules::addFunction("oxuser", "setPassword", "{ return true;}");
        oxTestModules::addFunction("oxuser", "createUser", "{ return true;}");
        oxTestModules::addFunction("oxuser", "load", "{ return true;}");
        oxTestModules::addFunction("oxuser", "changeUserData", "{ return true;}");
        oxTestModules::addFunction("oxuser", "setCreditPointsForRegistrant", "{ throw new Exception('setCreditPointsForRegistrant');}");

        $this->getSession()->setVariable('su', 'testUser');
        $this->getSession()->setVariable('re', 'testUser');
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock('oxcmp_user', array('_getDelAddressData', 'getParent'));
        $oUserView->expects($this->once())->method('_getDelAddressData');
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));

        try {
            $oUserView->createUser();
        } catch (Exception $oExcp) {
            $this->assertEquals('setCreditPointsForRegistrant', $oExcp->getMessage(), "Error while running testCreateUserForInvitationFeature");

            return;
        }
        $this->fail("Error while running testCreateUserForInvitationFeature");
    }

    /**
     * Test view logout().
     *
     * @return null
     */
    public function testCreateUserNotConfirmedAGB()
    {
        $this->getConfig()->setConfigParam('blConfirmAGB', true);

        $oUtilsView = $this->getMock('oxUtilsView', array("addErrorToDisplay"));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo('READ_AND_CONFIRM_TERMS'), $this->equalTo(false), $this->equalTo(true));
        oxRegistry::set('oxUtilsView', $oUtilsView);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('getParent'));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertFalse($oUserView->createUser());
    }

    public function testSetAndGetLoginStatus()
    {
        $iStatus = 999;

        $oCmp = oxNew('oxcmp_user');
        $this->assertNull($oCmp->getLoginStatus());

        $oCmp->setLoginStatus($iStatus);
        $this->assertEquals($iStatus, $oCmp->getLoginStatus());
    }

    public function testGetLogoutLink()
    {
        // note: modConfig mock fails for php 520
        $oConfig = $this->getMock('oxConfig', array('getShopHomeUrl', 'isSsl'));
        $oConfig->expects($this->any())->method('getShopHomeUrl')->will($this->returnValue('shopurl/?'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $oView = $this->getMock('Unit\Application\Controller\modcmp_user', array('getConfig'));
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->setRequestParameter('cl', 'testclass');
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('mnid', 'manId');
        $this->setRequestParameter('anid', 'artid');
        $this->setRequestParameter('tpl', 'test');
        $this->setRequestParameter('oxloadid', 'test');
        $this->setRequestParameter('recommid', 'recommid');
        $sLink = $oView->getLogoutLink();
        $sExpLink = "shopurl/?cl=testclass&amp;searchparam=a&amp;anid=artid&amp;cnid=catid&amp;mnid=manId" .
                    "&amp;tpl=test&amp;oxloadid=test&amp;recommid=recommid&amp;fnc=logout";

        $this->assertEquals($sExpLink, $sLink);
    }

    public function testGetLogoutLinkIfSsl()
    {
        $oConfig = $this->getMock('oxConfig', array('getShopSecureHomeUrl', 'getShopHomeUrl', 'isSsl'));
        $oConfig->expects($this->any())->method('getShopSecureHomeUrl')->will($this->returnValue('sslshopurl/?'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $oView = $this->getMock('Unit\Application\Controller\modcmp_user', array('getConfig'));
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $this->setRequestParameter('cl', 'testclass');
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('mnid', 'manId');
        $this->setRequestParameter('anid', 'artid');
        $this->setRequestParameter('tpl', 'test');
        $sLink = $oView->getLogoutLink();
        $sExpLink = "sslshopurl/?cl=testclass&amp;searchparam=a&amp;anid=artid&amp;cnid=catid&amp;mnid=manId" .
                    "&amp;tpl=test&amp;fnc=logout";

        $this->assertEquals($sExpLink, $sLink);
    }

    public function testChangeUserNoRedirectChecksSessionChallenge()
    {
        $oS = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));
        $oCU = $this->getMock('oxcmp_user', array('getUser', 'getSession'));
        $oCU->expects($this->never())->method('getUser')->will($this->returnValue(false));
        $oCU->expects($this->once())->method('getSession')->will($this->returnValue($oS));

        $this->assertSame(null, $oCU->UNITchangeUser_noRedirect());

        $oS = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oCU = $this->getMock('oxcmp_user', array('getUser', 'getSession'));
        $oCU->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $oCU->expects($this->once())->method('getSession')->will($this->returnValue($oS));

        $this->assertSame(null, $oCU->UNITchangeUser_noRedirect());
    }

    // FS#1925
    public function testBlockedUser()
    {
        $myDB = oxDb::getDB();
        $sTable = getViewName('oxuser');
        $iLastCustNr = ( int ) $myDB->getOne('select max( oxcustnr ) from ' . $sTable) + 1;
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'), oxField::T_RAW);
        $oUser->oxuser__oxcustnr = new oxField($iLastCustNr + 1, oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $oUser->save();
        $sQ = 'insert into oxaddress ( oxid, oxuserid, oxaddressuserid, oxcountryid ) values ( "test_user", "' . $oUser->getId() . '", "' . $oUser->getId() . '", "testCountry" ) ';
        $myDB->Execute($sQ);

        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testBlockedUser', 123 );}");

        $oUser2 = oxNew('oxuser');
        $oUser2->load($oUser->getId());
        $oUser2->login('test@oxid-esales.com', crc32('Test@oxid-esales.com'));

        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . $oUser2->getId() . '", "' . $this->getConfig()->getShopId() . '", "' . $oUser2->getId() . '", "oxidblocked" )';
        $myDB->Execute($sQ);

        try {
            $oUserView = oxNew('oxcmp_user');
            $oUserView->init();
        } catch (Exception $oE) {
            if ($oE->getCode() === 123) {
                $oUser2->logout();

                return;
            }
        }
        $oUser->logout();
        $this->fail('first assert should throw an exception');
    }

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter('invadr', 'testadr');
        $this->setRequestParameter('deladr', 'testdeladr');
        $this->setRequestParameter('reloadaddress', false);
        $this->setRequestParameter('lgn_usr', 'testuser');
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = oxNew('oxUbase');

        $oUserView = $this->getMock('oxcmp_user', array('getParent', 'getUser'));
        $oUserView->expects($this->atLeastOnce())->method('getParent')->will($this->returnValue($oParent));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue("testUser"));
        $this->assertEquals('testUser', $oUserView->render());
    }

    /**
     * Test view _loadSessionUser().
     *
     * @return null
     */
    public function testLoadSessionUser()
    {
        $this->setRequestParameter('blPerfNoBasketSaving', false);
        $oBasket = $this->getMock('oxBasket', array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oUser = $this->getMock('oxcmp_user', array('inGroup', 'isLoadedFromCookie'));
        $oUser->expects($this->once())->method('inGroup')->will($this->returnValue(false));
        $oUser->expects($this->once())->method('isLoadedFromCookie')->will($this->returnValue("testUser"));
        $oUserView = $this->getMock('oxcmp_user', array('getSession', 'getUser'));
        $oUserView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oUserView->UNITloadSessionUser();
    }

    /**
     * Test view _loadSessionUser().
     *
     * @return null
     */
    public function testLoadSessionUserIfUserIsNotSet()
    {
        $oUserView = $this->getMock('oxcmp_user', array('getSession', 'getUser'));
        $oUserView->expects($this->never())->method('getSession');
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $this->assertNull($oUserView->UNITloadSessionUser());
    }

    /**
     * Test login.
     *
     * @return null
     */
    public function testLogin()
    {
        oxTestModules::addFunction("oxUser", "login", "{ return true;}");
        $this->setRequestParameter('lgn_usr', 'test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd', crc32('Test@oxid-esales.com'));

        $oUserView = $this->getMock('oxcmp_user', array('_afterLogin'));
        $oUserView->expects($this->atLeastOnce())->method('_afterLogin')->will($this->returnValue("nextStep"));
        $this->assertEquals('nextStep', $oUserView->login());
    }

    /**
     * Test login.
     *
     * @return null
     */
    public function testLoginUserException()
    {
        oxTestModules::addFunction("oxUser", "login", "{ throw new oxUserException( 'testWrongUser', 123 );}");

        $oUserView = oxNew('oxcmp_user');
        $this->assertEquals('user', $oUserView->login());
    }

    /**
     * Test login.
     *
     * @return null
     */
    public function testLoginCookieException()
    {
        oxTestModules::addFunction("oxUser", "login", "{ throw new oxCookieException( 'testWrongUser', 123 );}");

        $oUserView = oxNew('oxcmp_user');
        $this->assertEquals('user', $oUserView->login());
    }

    /**
     * Test _afterlogin().
     *
     * @return null
     */
    public function testAfterLogin()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Skip CE/PE related tests for EE edition");
        }

        $this->setRequestParameter('blPerfNoBasketSaving', true);
        $oBasket = $this->getMock('oxBasket', array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');

        $oSession = $this->getMock('oxSession', array('getBasket', "regenerateSessionId"));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oSession->expects($this->once())->method('regenerateSessionId');

        $oUser = $this->getMock('oxcmp_user', array('inGroup'));
        $oUser->expects($this->once())->method('inGroup')->will($this->returnValue(false));

        $aMockFnc = array('getSession', "getLoginStatus");
        $oUserView = $this->getMock('oxcmp_user', $aMockFnc);
        $oUserView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->once())->method('getLoginStatus')->will($this->returnValue(1));
        $this->assertEquals('payment', $oUserView->UNITafterLogin($oUser));
    }

    /**
     * Test _afterlogin().
     *
     * @return null
     */
    public function testAfterLoginIfBlockedUser()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testBlockedUser', 123 );}");
        $oUser = $this->getMock('oxcmp_user', array('inGroup'));
        $oUser->expects($this->once())->method('inGroup')->will($this->returnValue(true));

        try {
            $oUserView = oxNew('oxcmp_user');
            $oUserView->UNITafterLogin($oUser);
        } catch (Exception $oE) {
            if ($oE->getCode() === 123) {
                return;
            }
        }
        $this->fail('first assert should throw an exception');
    }

    /**
     * Test login.
     *
     * @return null
     */
    public function testLoginNoRedirect()
    {
        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock('oxcmp_user', array('login', 'getParent'));
        $oUserView->expects($this->once())->method('login')->will($this->returnValue("nextStep"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertNull($oUserView->login_noredirect());
    }

    /**
     * Test view _afterLogout().
     *
     * @return null
     */
    public function testAfterLogout()
    {
        $this->getSession()->setVariable('paymentid', 'test');
        $this->getSession()->setVariable('sShipSet', 'test');
        $this->getSession()->setVariable('deladrid', 'test');
        $this->getSession()->setVariable('dynvalue', 'test');
        $oBasket = $this->getMock('oxBasket', array('onUpdate', 'resetUserInfo'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oBasket->expects($this->once())->method('resetUserInfo');
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oUserView = $this->getMock('oxcmp_user', array('getSession'));
        $oUserView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->UNITafterLogout();
        $this->assertNull(oxRegistry::getSession()->getVariable('paymentid'));
        $this->assertNull(oxRegistry::getSession()->getVariable('sShipSet'));
        $this->assertNull(oxRegistry::getSession()->getVariable('deladrid'));
        $this->assertNull(oxRegistry::getSession()->getVariable('dynvalue'));
    }

    /**
     * Test _afterlogin().
     *
     * @return null
     */
    public function testLogout()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Skip CE/PE related tests for EE edition");
        }

        oxTestModules::addFunction("oxUtils", "redirect", "{ return true;}");
        oxTestModules::addFunction("oxUser", "logout", "{ return true;}");
        $this->setRequestParameter('redirect', true);
        $blParam = $this->getConfig()->getConfigParam('sSSLShopURL');
        $this->getConfig()->setConfigParam('sSSLShopURL', true);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $aMockFnc = array('_afterLogout', '_getLogoutLink', 'getParent');
        $oUserView = $this->getMock('oxcmp_user', $aMockFnc);
        $oUserView->expects($this->once())->method('_afterLogout');
        $oUserView->expects($this->once())->method('_getLogoutLink')->will($this->returnValue("testurl"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));

        $oUserView->logout();
        $this->assertEquals(3, $oUserView->getLoginStatus());
        $this->getConfig()->setConfigParam('sSSLShopURL', $blParam);
    }

    /**
     * Test changeUser().
     *
     * @return null
     */
    public function testChangeUser()
    {
        $oUserView = $this->getMock('oxcmp_user', array('_changeUser_noRedirect'));
        $oUserView->expects($this->once())->method('_changeUser_noRedirect')->will($this->returnValue(true));
        $this->assertEquals('payment', $oUserView->changeUser());
    }

    /**
     * Test changeUser().
     *
     * @return null
     */
    public function testChangeUserIfNotRegisteredUser()
    {
        $oUserView = $this->getMock('oxcmp_user', array('_changeUser_noRedirect'));
        $oUserView->expects($this->once())->method('_changeUser_noRedirect')->will($this->returnValue(false));
        $this->assertFalse($oUserView->changeUser());
    }

    /**
     * Test changeUser().
     *
     * @return null
     */
    public function testChangeUserTestValues()
    {
        $oUserView = $this->getMock('oxcmp_user', array('_changeUser_noRedirect'));
        $oUserView->expects($this->once())->method('_changeUser_noRedirect')->will($this->returnValue(true));
        $this->assertEquals('account_user', $oUserView->changeuser_testvalues());
    }

    /**
     * Test changeUser() on error.
     *
     * @return null
     */
    public function testChangeUserTestValuesOnError()
    {
        $oUserView = $this->getMock('oxcmp_user', array('_changeUser_noRedirect'));
        $oUserView->expects($this->once())->method('_changeUser_noRedirect')->will($this->returnValue(null));
        $this->assertEquals(null, $oUserView->changeuser_testvalues());
    }

    /**
     * Test createUser().
     *
     * @return null
     */
    public function testCreateUser()
    {
        oxTestModules::addFunction("oxemail", "sendRegisterEmail", "{ return true;}");
        $this->setRequestParameter('lgn_usr', 'test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd', 'Test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd2', 'Test@oxid-esales.com');
        $this->setRequestParameter('order_remark', 'TestRemark');
        $this->setRequestParameter('option', 3);
        $aRawVal = array('oxuser__oxfname'     => 'fname',
                         'oxuser__oxlname'     => 'lname',
                         'oxuser__oxstreetnr'  => 'nr',
                         'oxuser__oxstreet'    => 'street',
                         'oxuser__oxzip'       => 'zip',
                         'oxuser__oxcity'      => 'city',
                         'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');
        $this->setRequestParameter('invadr', $aRawVal);

        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('getParent'));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertEquals('payment?new_user=1&success=1', $oUserView->createUser());
        $this->assertEquals('TestRemark', oxRegistry::getSession()->getVariable('ordrem'));
        $this->assertTrue($oUserView->getNonPublicVar('_blIsNewUser'));
    }

    /**
     * Test createUser().
     *
     * @return null
     */
    public function testCreateUserWithoutPassword()
    {
        $this->setRequestParameter('lgn_usr', 'test@oxid-esales.com');
        $aRawVal = array('oxuser__oxfname'     => 'fname',
                         'oxuser__oxlname'     => 'lname',
                         'oxuser__oxstreetnr'  => 'nr',
                         'oxuser__oxstreet'    => 'street',
                         'oxuser__oxzip'       => 'zip',
                         'oxuser__oxcity'      => 'city',
                         'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');
        $this->setRequestParameter('invadr', $aRawVal);
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('_afterLogin', 'getParent'));
        $oUserView->expects($this->once())->method('_afterLogin');
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertEquals('payment?new_user=1&success=1', $oUserView->createUser());
        $this->assertTrue($oUserView->getNonPublicVar('_blIsNewUser'));
        $this->assertNotNull(oxRegistry::getSession()->getVariable('usr'));
    }

    /**
     * Test createUser().
     *
     * @return null
     */
    public function testCreateUserUserException()
    {
        oxTestModules::addFunction("oxuser", "checkValues", "{ throw new oxUserException( 'testBlockedUser', 123 );}");
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock('oxcmp_user', array("getParent"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertFalse($oUserView->createUser());
    }

    /**
     * Test createUser().
     *
     * @return null
     */
    public function testCreateUseroxInputException()
    {
        oxTestModules::addFunction("oxuser", "checkValues", "{ throw new oxInputException( 'testBlockedUser', 123 );}");

        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock('oxcmp_user', array("getParent"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertFalse($oUserView->createUser());
    }

    /**
     * Test createUser().
     *
     * @return null
     */
    public function testCreateUserConnectionException()
    {
        oxTestModules::addFunction("oxuser", "checkValues", "{ throw new oxConnectionException( 'testBlockedUser', 123 );}");

        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oParent = $this->getMock('oxubase', array("isEnabledPrivateSales"));
        $oParent->expects($this->once())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock('oxcmp_user', array("getParent"));
        $oUserView->expects($this->any())->method('getParent')->will($this->returnValue($oParent));
        $this->assertFalse($oUserView->createUser());
    }

    /**
     * Test registerUser().
     *
     * @return null
     */
    public function testRegisterUserWithProblems()
    {
        $oUserView = $this->getMock('oxcmp_user', array('createuser', 'logout'));
        $oUserView->expects($this->once())->method('createuser')->will($this->returnValue(false));
        $oUserView->expects($this->once())->method('logout')->will($this->returnValue(false));
        $this->assertNull($oUserView->registerUser());
    }

    /**
     * Test registerUser().
     *
     * @return null
     */
    public function testRegisterUser()
    {
        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('createuser', 'logout'));
        $oUserView->expects($this->once())->method('createuser')->will($this->returnValue("payment"));
        $oUserView->setNonPublicVar('_blIsNewUser', true);
        $this->assertEquals('register?success=1', $oUserView->registerUser());
    }

    /**
     * Test registerUser().
     *
     * @return null
     */
    public function testRegisterUserWithNewsletterError()
    {
        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), array('createuser', 'logout'));
        $oUserView->expects($this->once())->method('createuser')->will($this->returnValue("payment"));
        $oUserView->setNonPublicVar('_blIsNewUser', true);
        $oUserView->setNonPublicVar('_blNewsSubscriptionStatus', false);
        $this->assertEquals('register?success=1&newslettererror=4', $oUserView->registerUser());
    }

    /**
     * Test _changeUser_noRedirect()().
     *
     * @return null
     */
    public function testChangeUserNoRedirect()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Skip CE/PE related tests for EE edition");
        }

        $this->setRequestParameter('order_remark', 'TestRemark');
        $this->setRequestParameter('blnewssubscribed', null);
        $aRawVal = array('oxuser__oxfname'     => 'fname',
                         'oxuser__oxlname'     => 'lname',
                         'oxuser__oxstreetnr'  => 'nr',
                         'oxuser__oxstreet'    => 'street',
                         'oxuser__oxzip'       => 'zip',
                         'oxuser__oxcity'      => 'city',
                         'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');
        $this->setRequestParameter('invadr', $aRawVal);

        $oUser = $this->getMock('oxUser', array('changeUserData', 'getNewsSubscription', 'setNewsSubscription'));
        $oUser->expects($this->once())->method('changeUserData')->with(
            $this->equalTo('test@oxid-esales.com'),
            $this->equalTo(crc32('Test@oxid-esales.com')),
            $this->equalTo(crc32('Test@oxid-esales.com')),
            $this->equalTo($aRawVal),
            null
        );
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue(oxNew('oxnewssubscribed')));
        $oUser->expects($this->once())->method('setNewsSubscription')->will($this->returnValue(1));
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com');
        $oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'));
        $oBasket = $this->getMock('oxBasket', array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oSession = $this->getMock('oxSession', array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $aMockFnc = array('getSession', 'getUser', '_getDelAddressData');
        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), $aMockFnc);
        $oUserView->expects($this->once())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oUserView->UNITchangeUser_noRedirect());
        $this->assertEquals('TestRemark', oxRegistry::getSession()->getVariable('ordrem'));
        $this->assertEquals(1, $oUserView->getNonPublicVar('_blNewsSubscriptionStatus'));
    }

    /**
     * Test _changeUser_noRedirect().
     *
     * @return null
     */
    public function testChangeUserNoRedirectConnectionException()
    {
        oxTestModules::addFunction("oxuser", "changeUserData", "{ throw new oxConnectionException( 'testBlockedUser', 123 );}");
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oUserView = $this->getMock('oxcmp_user', array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->once())->method('_getDelAddressData');
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(new oxUser()));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $this->assertNull($oUserView->UNITchangeUser_noRedirect());
    }

    /**
     * Test _changeUser_noRedirect().
     *
     * @return null
     */
    public function testChangeUserNoRedirectInputException()
    {
        oxTestModules::addFunction("oxuser", "changeUserData", "{ throw new oxInputException( 'testBlockedUser', 123 );}");
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oUserView = $this->getMock('oxcmp_user', array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->once())->method('_getDelAddressData');
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(new oxUser()));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $this->assertNull($oUserView->UNITchangeUser_noRedirect());
    }

    /**
     * Test _changeUser_noRedirect().
     * 
     * @return null
     */
    public function testChangeUserNoRedirectUserException()
    {
        oxTestModules::addFunction("oxuser", "changeUserData", "{ throw new oxUserException( 'testBlockedUser', 123 );}");
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oUserView = $this->getMock('oxcmp_user', array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->once())->method('_getDelAddressData');
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue(new oxUser()));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $this->assertNull($oUserView->UNITchangeUser_noRedirect());
    }

    /**
     * Test _changeUser_noRedirect().
     *
     * @return null
     */
    public function testChangeUserNoRedirectSetUnsubscribed()
    {
        // Any big number, bigger then possible test data. Row will be deleted in teardown.
        $iLastCustNr = 999;
        $sPassword = $sPassword2 = crc32('_Test@oxid.de');
        $aInvAddress = array(
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $aDelAddress = array(
            'oxaddress__oxfname'     => 'fname',
            'oxaddress__oxlname'     => 'lname',
            'oxaddress__oxstreetnr'  => 'nr',
            'oxaddress__oxstreet'    => 'street',
            'oxaddress__oxzip'       => 'zip',
            'oxaddress__oxcity'      => 'city',
            'oxaddress__oxsal'       => 'MSR',
            'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $sUser = '_test@oxid.de';
        $oUser = oxNew('oxUser');
        $oUser->setId('_test_oxuserid');
        $oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField($sUser, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPassword);
        $oUser->oxuser__oxcustnr = new oxField($iLastCustNr, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $oUser->oxuser__oxcreate = new oxField(date('Y-m-d'), oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(date('Y-m-d'), oxField::T_RAW);
        $oUser->save();

        $oNewsSubscribed = oxNew('oxNewsSubscribed');
        $oNewsSubscribed->setId('_test_9191965231c39c27141aab0431');
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField('1', oxField::T_RAW);
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField('_test_oxuserid', oxField::T_RAW);
        $oNewsSubscribed->oxnewssubscribed__oxemail = new oxField('_test@oxid.de', oxField::T_RAW);
        $oNewsSubscribed->save();

        $oUser->changeUserData($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddress);
        $oUser->setNewsSubscription(false, false);

        $oNewsSubscribed->load('_test_9191965231c39c27141aab0431');
        $this->assertNotEquals($oNewsSubscribed->oxnewssubscribed__oxunsubscribed->value, '0000-00-00 00:00:00');
    }

    /**
     * Test _changeUser_noRedirect().
     * Change if unsubscription date changes when subscribtion wasn't confirmed.
     *
     * @return null
     */
    public function testChangeUserNoRedirectSetUnsubscribed_2()
    {
        // Any big number, bigger then possible test data. Row will be deleted in teardown.
        $iLastCustNr = 999;
        $sPassword = $sPassword2 = crc32('_Test@oxid.de');
        $aInvAddress = array(
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $aDelAddress = array(
            'oxaddress__oxfname'     => 'fname',
            'oxaddress__oxlname'     => 'lname',
            'oxaddress__oxstreetnr'  => 'nr',
            'oxaddress__oxstreet'    => 'street',
            'oxaddress__oxzip'       => 'zip',
            'oxaddress__oxcity'      => 'city',
            'oxaddress__oxsal'       => 'MSR',
            'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $sUser = '_test@oxid.de';
        $oUser = oxNew('oxUser');
        $oUser->setId('_test_oxuserid');
        $oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField($sUser, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPassword);
        $oUser->oxuser__oxcustnr = new oxField($iLastCustNr, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $oUser->oxuser__oxcreate = new oxField(date('Y-m-d'), oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(date('Y-m-d'), oxField::T_RAW);
        $oUser->save();

        $oNewsSubscribed = oxNew('oxNewsSubscribed');
        $oNewsSubscribed->setId('_test_9191965231c39c27141aab0431');
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField('2', oxField::T_RAW);
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField('_test_oxuserid', oxField::T_RAW);
        $oNewsSubscribed->oxnewssubscribed__oxemail = new oxField('_test@oxid.de', oxField::T_RAW);
        $oNewsSubscribed->save();

        $oUser->changeUserData($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddress);
        $oUser->setNewsSubscription(false, false);

        $oNewsSubscribed->load('_test_9191965231c39c27141aab0431');
        $this->assertNotEquals($oNewsSubscribed->oxnewssubscribed__oxunsubscribed->value, '0000-00-00 00:00:00');
    }

    /**
     * Test _changeUser_noRedirect()().
     *
     * @return null
     */
    public function testChangeUserNoRedirectCanNotChangeBlackListedData()
    {
        $this->setRequestParameter('blnewssubscribed', false);
        $aRawVal = array(
            'oxuser__oxid'        => 'newId',
            'oxid'        => 'newId',
            'oxuser__oxpoints'        => 'newPoints',
            'oxpoints'        => 'newPoints',
            'oxuser__oxboni'        => 'newBoni',
            'oxboni'        => 'newBoni',
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $this->setRequestParameter('invadr', $aRawVal);

        $oUser = $this->getMock($this->getProxyClassName('oxUser'), array('getNewsSubscription', 'setNewsSubscription'));
        $oUser->oxuser__oxid = new oxField('oldId');
        $oUser->oxuser__oxpoints = new oxField('oldPoints');
        $oUser->oxuser__oxboni = new oxField('oldBoni');
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com');
        $oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'));

        $oSession = $this->getMock('oxSession', array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getDelAddressData'));
        $oUserView->expects($this->once())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oUserView->UNITchangeUser_noRedirect());

        $this->assertEquals('oldId', $oUser->oxuser__oxid->value);
        $this->assertEquals('oldPoints', $oUser->oxuser__oxpoints->value);
        $this->assertEquals('oldBoni', $oUser->oxuser__oxboni->value);
    }

    /**
     * Test _changeUser_noRedirect()().
     *
     * @return null
     */
    public function testChangeUserNoRedirectCanNotChangeBlackListedDataUsingUppercaseLetters()
    {
        $this->setRequestParameter('blnewssubscribed', false);
        $aRawVal = array(
            'OXID'        => 'newId',
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $this->setRequestParameter('invadr', $aRawVal);
        $oUser = $this->getMock($this->getProxyClassName('oxUser'), array('getNewsSubscription', 'setNewsSubscription'));
        $oUser->oxuser__oxid = new oxField('oldId');
        $oUser->oxuser__oxpoints = new oxField('oldPoints');
        $oUser->oxuser__oxboni = new oxField('oldBoni');
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com');
        $oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'));
        $oSession = $this->getMock('oxSession', array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getDelAddressData'));
        $oUserView->expects($this->once())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oUserView->UNITchangeUser_noRedirect());
        $this->assertEquals('oldId', $oUser->oxuser__oxid->value);
    }

    /**
     * Test _getDelAddressData().
     *
     * @return null
     */
    public function testGetDelAddressData()
    {
        $aRawVal = array('oxaddress__oxfname'     => 'fname',
                         'oxaddress__oxlname'     => 'lname',
                         'oxaddress__oxstreetnr'  => 'nr',
                         'oxaddress__oxstreet'    => 'street',
                         'oxaddress__oxzip'       => 'zip',
                         'oxaddress__oxcity'      => 'city',
                         'oxaddress__oxsal'       => 'MSR',
                         'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984');

        $this->setRequestParameter('deladr', $aRawVal);
        $this->setRequestParameter('blshowshipaddress', true);
        $oUserView = $this->getProxyClass("oxcmp_user");
        $this->assertEquals($aRawVal, $oUserView->UNITgetDelAddressData());
    }

    /**
     * Test _getDelAddressData().
     *
     * @return null
     */
    public function testGetDelAddressDataIfDataNotSet()
    {
        $aRawVal = array('oxaddress__oxsal' => 'MSR');
        $this->setRequestParameter('deladr', $aRawVal);
        $oUserView = $this->getProxyClass("oxcmp_user");
        $this->assertEquals(array(), $oUserView->UNITgetDelAddressData());
    }

    /**
     * Test init().
     *
     * @return null
     */
    public function testInit()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oUserView = $this->getMock('oxcmp_user', array('_loadSessionUser'));
        $oUserView->expects($this->any())->method('_loadSessionUser');
        $oUserView->init();
    }

    /**
     * Test checking private sales login - if redirecting is done without checking
     * if redirection already was done (#2040)
     *
     * @return null
     */
    public function testCheckPsState()
    {
        $oConfig = $this->getConfig();
        $sPageUrl = $oConfig->getShopHomeURL() . "cl=account";
        $oConfig->setConfigParam("blPsLoginEnabled", true);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($sPageUrl), $this->equalTo(false));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oTestView = oxNew("account");

        $oView = $this->getMock('oxcmp_user', array('getUser', 'getParent'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(null));
        $oView->expects($this->atLEastOnce())->method('getParent')->will($this->returnValue($oTestView));

        $oView->UNITcheckPsState();
    }

    /**
     * Test oxcmp_user::createUser() - try to save password with spec chars.
     * #0003680
     *
     * @return null
     */
    public function testCreateUser_setPasswordWithSpecChars()
    {
        $this->setExpectedException('oxException', 'Create user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('lgn_usr', 'test_username');
        $this->setRequestParameter('lgn_pwd', $sPass);
        $this->setRequestParameter('lgn_pwd2', $sPass);
        $this->setRequestParameter('invadr', null);

        $oUser = $this->getMock('oxUser', array('checkValues'));
        $oUser->expects($this->once())
            ->method('checkValues')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(null), $this->equalTo(null))
            ->will($this->throwException(new oxException('Create user test')));
        oxTestModules::addModuleObject('oxuser', $oUser);

        $oParent = $this->getMock('oxView', array('isEnabledPrivateSales'));

        $oView = $this->getMock('oxcmp_user', array('_getDelAddressData', 'getParent'));
        $oView->expects($this->once())->method('getParent')->will($this->returnValue($oParent));
        $oView->createUser();
    }

    /**
     * Test oxcmp_user::_changeUser_noRedirect() - try to save password with spec chars.
     * #0003680
     *
     * @return null
     */
    public function testChangeUser_noRedirect_setPasswordWithSpecChars()
    {
        $this->setExpectedException('oxException', 'Change user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('invadr', null);

        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oUser = $this->getMock('oxUser', array('changeUserData'));
        $oUser->oxuser__oxusername = new oxField('test_username', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPass, oxField::T_RAW);
        $oUser->expects($this->once())
            ->method('changeUserData')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(null), $this->equalTo(null))
            ->will($this->throwException(new oxException('Change user test')));

        $oView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('_getDelAddressData', 'getUser', 'getSession'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oView->UNITchangeUser_noRedirect();
    }

    /**
     * Test oxcmp_user::login() - try to login with password with spec chars.
     * #0003680
     *
     * @return null
     */
    public function testLogin_setPasswordWithSpecChars()
    {
        $this->setExpectedException('oxException', 'Login user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('lgn_usr', 'test_username');
        $this->setRequestParameter('lgn_pwd', $sPass);
        $this->setRequestParameter('lgn_cook', null);

        $oUser = $this->getMock('oxUser', array('login'));
        $oUser->expects($this->once())
            ->method('login')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo(null))
            ->will($this->throwException(new oxException('Login user test')));
        oxTestModules::addModuleObject('oxuser', $oUser);

        $oView = $this->getMock('oxcmp_user', array('setLoginStatus'));
        $oView->login();
    }
}
