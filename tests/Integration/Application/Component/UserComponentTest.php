<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use \oxcmp_user;
use \Exception;
use \oxField;
use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
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

class UserComponentTest extends \OxidTestCase
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getInvitor"), array(), '', false);
        $oView->expects($this->atLeastOnce())->method('getInvitor');
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

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getShopHomeURL"));
        $oConfig->expects($this->atLeastOnce())->method('getShopHomeURL')->will($this->returnValue("testUrl"));

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("getClassName", "isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('getClassName')->will($this->returnValue("test"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        try {
            // testing..
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getUser", "getConfig", "getParent"), array(), '', false);
            $oView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(false));
            $oView->expects($this->atLeastOnce())->method('getParent')->will($this->returnValue($oParent));
            $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
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

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getShopHomeURL"));
        $oConfig->expects($this->any())->method('getShopHomeURL')->will($this->returnValue("testUrl"));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("isTermsAccepted"));
        $oUser->expects($this->any())->method('isTermsAccepted')->will($this->throwException(new Exception("isTermsAccepted")));

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("getClassName", "isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('getClassName')->will($this->returnValue("test"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getUser", "getConfig", "_checkTermVersion", "getParent"), array(), '', false);
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

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getShopHomeURL", "getConfigParam"));
        $oConfig->expects($this->any())->method('getShopHomeURL')->will($this->returnValue("testUrl"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("isTermsAccepted"));
        $oUser->expects($this->any())->method('isTermsAccepted')->will($this->returnValue(false));

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("getClassName"));
        $oParent->expects($this->atLeastOnce())->method('getClassName')->will($this->returnValue("test"));

        try {
            // testing..
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getUser", "getConfig", "getParent"), array(), '', false);
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, $aMockFnc);
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

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("acceptTerms"));
        $oUser->expects($this->once())->method('acceptTerms');

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('login', 'getUser', 'getParent'));
        $oUserView->expects($this->never())->method('login');
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
        $oUserView->expects($this->atLeastOnce())->method('getParent')->will($this->returnValue($oParent));
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_getDelAddressData', 'getParent'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData');
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

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo('READ_AND_CONFIRM_TERMS'), $this->equalTo(false), $this->equalTo(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsView::class, $oUtilsView);

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(true));

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
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopHomeUrl', 'isSsl'));
        $oConfig->expects($this->any())->method('getShopHomeUrl')->will($this->returnValue('shopurl/?'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Integration\Application\Component\modcmp_user::class, array('getConfig'));
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
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopSecureHomeUrl', 'getShopHomeUrl', 'isSsl'));
        $oConfig->expects($this->any())->method('getShopSecureHomeUrl')->will($this->returnValue('sslshopurl/?'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Integration\Application\Component\modcmp_user::class, array('getConfig'));
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
        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(false));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oS);

        $oCU = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', 'getSession'));
        $oCU->expects($this->never())->method('getUser')->will($this->returnValue(false));
        $oCU->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oS));

        $this->assertSame(null, $oCU->UNITchangeUser_noRedirect());

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oS);

        $oCU = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', 'getSession'));
        $oCU->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(false));
        $oCU->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oS));

        $this->assertSame(null, $oCU->UNITchangeUser_noRedirect());
    }

    /**
     * FS#1925
     */
    public function testBlockedUser()
    {
        $myDB = oxDb::getDB();
        $sTable = getViewName('oxuser');
        $iLastCustNr = ( int ) $myDB->getOne('select max( oxcustnr ) from ' . $sTable) + 1;
        $oUser = oxNew('oxuser');
        $salt = md5('salt');
        $paswordHash = $oUser->encodePassword('secret', $salt);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($paswordHash);
        $oUser->oxuser__oxpasssalt = new oxField($salt, oxField::T_RAW);
        $oUser->oxuser__oxcustnr = new oxField($iLastCustNr + 1, oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $oUser->save();
        $sQ = 'insert into oxaddress ( oxid, oxuserid, oxaddressuserid, oxcountryid ) values ( "test_user", "' . $oUser->getId() . '", "' . $oUser->getId() . '", "testCountry" ) ';
        $myDB->Execute($sQ);

        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testBlockedUser', 123 );}");

        $oUser2 = oxNew('oxuser');
        $oUser2->load($oUser->getId());
        $oUser2->login('test@oxid-esales.com', 'secret');

        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . $oUser2->getId() . '", "' . $this->getConfig()->getShopId() . '", "' . $oUser2->getId() . '", "oxidblocked" )';
        $myDB->Execute($sQ);

        try {
            $oUserView = oxNew('oxcmp_user');
            $oUserView->init();
        } catch (Exception $oE) {
            if ($oE->getCode() === 123) {
                $oUser2->logout();
                $exceptionThrown = true;
            }
        }
        $oUser->logout();
        $this->assertTrue($exceptionThrown, 'first assert should throw an exception');
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

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getParent', 'getUser'));
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
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oSession->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('inGroup', 'isLoadedFromCookie'));
        $oUser->expects($this->once())->method('inGroup')->will($this->returnValue(false));
        $oUser->expects($this->atLeastOnce())->method('isLoadedFromCookie')->will($this->returnValue("testUser"));
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getSession', 'getUser'));
        $oUserView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
        $oUserView->UNITloadSessionUser();
    }

    /**
     * Test view _loadSessionUser().
     *
     * @return null
     */
    public function testLoadSessionUserIfUserIsNotSet()
    {
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getSession', 'getUser'));
        $oUserView->expects($this->never())->method('getSession');
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(false));
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

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_afterLogin'));
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
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', "regenerateSessionId"));
        $oSession->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        $oSession->expects($this->once())->method('regenerateSessionId');

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('inGroup'));
        $oUser->expects($this->once())->method('inGroup')->will($this->returnValue(false));

        $aMockFnc = array('getSession', "getLoginStatus");
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, $aMockFnc);
        $oUserView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getLoginStatus')->will($this->returnValue(1));
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
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('inGroup'));
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
        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('login', 'getParent'));
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
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('onUpdate', 'resetUserInfo'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oBasket->expects($this->once())->method('resetUserInfo');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oSession->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getSession'));
        $oUserView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $aMockFnc = array('_afterLogout', '_getLogoutLink', 'getParent');
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, $aMockFnc);
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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_changeUser_noRedirect'));
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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_changeUser_noRedirect'));
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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_changeUser_noRedirect'));
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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_changeUser_noRedirect'));
        $oUserView->expects($this->once())->method('_changeUser_noRedirect')->will($this->returnValue(null));
        $this->assertEquals(null, $oUserView->changeuser_testvalues());
    }

    /**
     * Test createUser().
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getParent"));
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getParent"));
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

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array("isEnabledPrivateSales"));
        $oParent->expects($this->atLeastOnce())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array("getParent"));
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
        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('createuser', 'logout'));
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

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('changeUserData', 'getNewsSubscription', 'setNewsSubscription'));
        $oUser->expects($this->once())->method('changeUserData')->with(
            $this->equalTo('test@oxid-esales.com'),
            $this->equalTo(crc32('Test@oxid-esales.com')),
            $this->equalTo(crc32('Test@oxid-esales.com')),
            $this->equalTo($aRawVal),
            null
        );
        $oUser->expects($this->atLeastOnce())->method('getNewsSubscription')->will($this->returnValue(oxNew('oxnewssubscribed')));
        $oUser->expects($this->once())->method('setNewsSubscription')->will($this->returnValue(1));
        $oUser->oxuser__oxusername = new oxField('test@oxid-esales.com');
        $oUser->oxuser__oxpassword = new oxField(crc32('Test@oxid-esales.com'));
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        $aMockFnc = array('getSession', 'getUser', '_getDelAddressData');
        $oUserView = $this->getMock($this->getProxyClassName("oxcmp_user"), $aMockFnc);
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData');
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(new oxUser()));
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData');
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(new oxUser()));
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', '_getDelAddressData', 'getSession'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData');
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(new oxUser()));
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
            'oxuser__oxfname'        => 'fname',
            'oxuser__oxlname'        => 'lname',
            'oxuser__oxstreet'       => 'street',
            'oxuser__oxstreetnr'     => 'nr',
            'oxuser__oxzip'          => 'zip',
            'oxuser__oxcity'         => 'city',
            'oxuser__oxcountryid'    => 'a7c40f631fc920687.20179984'
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
            // Existing fields which users should not be able to change.
            'oxuser__oxid'        => 'newId',
            'oxid'                => 'newId',
            'oxuser__oxpoints'    => 'newPoints',
            'oxpoints'            => 'newPoints',
            'oxuser__oxboni'      => 'newBoni',
            'oxboni'              => 'newBoni',

            // Fields which users should be capable to change.
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

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getDelAddressData'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
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
            'OXID'                => 'newId',
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getDelAddressData'));
        $oUserView->expects($this->atLeastOnce())->method('_getDelAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
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

    public function testChangeUserBillingAddress()
    {
        $this->setRequestParameter('blnewssubscribed', false);
        $this->setRequestParameter('blshowshipaddress', true);
        $formFields = array(
            // Existing fields which users should not be able to change.
            'oxuser__oxid'        => 'newId',
            'oxid'                => 'newId',
            'oxuser__oxpoints'    => 'newPoints',
            'oxpoints'            => 'newPoints',
            'oxuser__oxboni'      => 'newBoni',
            'oxboni'              => 'newBoni',

            // By default, user should not be capable to change new fields.
            'oxaddress__newfield' => 'newId',
            'newfield'            => 'newId',

            // Fields which users should be capable to change.
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        );
        $this->setRequestParameter('invadr', $formFields);

        $expectedUserData = [
            'oxuser__oxfname'     => 'fname',
            'oxuser__oxlname'     => 'lname',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxstreet'    => 'street',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984'
        ];

        $user = $this->getMock($this->getProxyClassName('oxUser'), ['changeUserData']);
        $user->expects($this->any())->method('changeUserData')->with(
            $this->anything(),
            $this->anything(),
            $this->anything(),
            $this->equalTo($expectedUserData),
            $this->anything()
        );

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getBillingAddressData'));
        $oUserView->expects($this->any())->method('_getBillingAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($user));
        $this->assertTrue($oUserView->UNITchangeUser_noRedirect());
    }

    public function testChangeUserDeliveryAddress()
    {
        $this->setRequestParameter('blnewssubscribed', false);
        $this->setRequestParameter('blshowshipaddress', true);
        $formFields = array(
            // Existing fields which users should not be able to change.
            'oxaddress__oxid'            => 'newId',
            'oxid'                       => 'newId',
            'oxaddress__oxuserid'        => 'newId',
            'oxuserid'                   => 'newId',
            'oxaddress__oxaddressuserid' => 'newId',
            'oxaddressuserid'            => 'newId',

            // By default, user should not be capable to change new fields.
            'oxaddress__newfield'        => 'newId',
            'newfield'                   => 'newId',

            // Fields which users should be capable to change.
            'oxaddress__oxfname'         => 'fname',
            'oxaddress__oxlname'         => 'lname',
            'oxaddress__oxstreetnr'      => 'nr',
            'oxaddress__oxstreet'        => 'street',
            'oxaddress__oxzip'           => 'zip',
            'oxaddress__oxcity'          => 'city',
            'oxaddress__oxcountryid'     => 'a7c40f631fc920687.20179984'
        );
        $this->setRequestParameter('deladr', $formFields);

        $expectedUserData = [
            'oxaddress__oxfname'         => 'fname',
            'oxaddress__oxlname'         => 'lname',
            'oxaddress__oxstreetnr'      => 'nr',
            'oxaddress__oxstreet'        => 'street',
            'oxaddress__oxzip'           => 'zip',
            'oxaddress__oxcity'          => 'city',
            'oxaddress__oxcountryid'     => 'a7c40f631fc920687.20179984'
        ];

        $user = $this->getMock($this->getProxyClassName('oxUser'), ['changeUserData']);
        $user->expects($this->any())->method('changeUserData')->with(
            $this->anything(),
            $this->anything(),
            $this->anything(),
            $this->anything(),
            $this->equalTo($expectedUserData)
        );

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket', 'checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUserView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('getSession', 'getUser', '_getBillingAddressData'));
        $oUserView->expects($this->any())->method('_getBillingAddressData')->will($this->returnValue(null));
        $oUserView->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oUserView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($user));
        $this->assertTrue($oUserView->UNITchangeUser_noRedirect());
    }

    /**
     * Test init().
     *
     * @return null
     */
    public function testInit()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oUserView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_loadSessionUser'));
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

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($sPageUrl), $this->equalTo(false));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oTestView = oxNew("account");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('getUser', 'getParent'));
        $oView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue(null));
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
        $this->expectException('oxException');
        $this->expectExceptionMessage('Create user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('lgn_usr', 'test_username');
        $this->setRequestParameter('lgn_pwd', $sPass);
        $this->setRequestParameter('lgn_pwd2', $sPass);
        $this->setRequestParameter('invadr', null);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('checkValues'));
        $oUser->expects($this->once())
            ->method('checkValues')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(null), $this->equalTo(null))
            ->will($this->throwException(new oxException('Create user test')));
        oxTestModules::addModuleObject('oxuser', $oUser);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('isEnabledPrivateSales'));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('_getDelAddressData', 'getParent'));
        $oView->expects($this->atLeastOnce())->method('getParent')->will($this->returnValue($oParent));
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
        $this->expectException('oxException');
        $this->expectExceptionMessage('Change user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('invadr', null);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->atLeastOnce())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('changeUserData'));
        $oUser->oxuser__oxusername = new oxField('test_username', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPass, oxField::T_RAW);
        $oUser->expects($this->once())
            ->method('changeUserData')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(null), $this->equalTo(null))
            ->will($this->throwException(new oxException('Change user test')));

        $oView = $this->getMock($this->getProxyClassName('oxcmp_user'), array('_getDelAddressData', 'getUser', 'getSession'));
        $oView->expects($this->atLeastOnce())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
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
        $this->expectException('oxException');
        $this->expectExceptionMessage('Login user test');

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('lgn_usr', 'test_username');
        $this->setRequestParameter('lgn_pwd', $sPass);
        $this->setRequestParameter('lgn_cook', null);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('login'));
        $oUser->expects($this->once())
            ->method('login')
            ->with($this->equalTo('test_username'), $this->equalTo($sPass), $this->equalTo(null))
            ->will($this->throwException(new oxException('Login user test')));
        oxTestModules::addModuleObject('oxuser', $oUser);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\UserComponent::class, array('setLoginStatus'));
        $oView->login();
    }

    /**
     * Test address trimming oxcmp_user::changeUserNoRedirect()
     *
     * #0006693
     */
    public function testChangeUserNoRedirectAddressTrimming()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Skip CE/PE related tests for EE edition");
        }

        $untrimmedInvoiceAddress = [
            'oxuser__oxfname'   => ' Simon ',
            'oxuser__oxlname'   => ' de la Serna ',
        ];

        $trimmedInvoiceAddress = [
            'oxuser__oxfname'   => 'Simon',
            'oxuser__oxlname'   => 'de la Serna',
        ];

        $untrimmedDeliveryAddress = [
            'oxaddress__oxfname'   => ' Simon ',
            'oxaddress__oxlname'   => ' de la Serna ',
        ];

        $trimmedDeliveryAddress = [
            'oxaddress__oxfname'   => 'Simon',
            'oxaddress__oxlname'   => 'de la Serna',
        ];

        $this->setRequestParameter('invadr', $untrimmedInvoiceAddress);
        $this->setRequestParameter('deladr', $untrimmedDeliveryAddress);
        $this->setRequestParameter('blshowshipaddress', true);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session
            ->expects($this->atLeastOnce())
            ->method('checkSessionChallenge')
            ->will($this->returnValue(true));

        $userComponent = $this->getMock(
            $this->getProxyClassName("oxcmp_user"),
            [
                'getSession',
                'getUser'
            ]
        );

        $userComponent
            ->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));

        $user = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['changeUserData']);
        $user
            ->expects($this->once())
            ->method('changeUserData')
            ->with(
                null,
                null,
                null,
                $this->equalTo($trimmedInvoiceAddress),
                $this->equalTo($trimmedDeliveryAddress)
            );

        $userComponent
            ->expects($this->atLeastOnce())
            ->method('getUser')
            ->will($this->returnValue($user));

        $userComponent->UNITchangeUser_noRedirect();
    }

    /**
     * Test address trimming oxcmp_user::createUser()
     *
     * #0006693
     */
    public function testCreateUserAddressTrimming()
    {
        oxTestModules::addFunction("oxemail", "sendRegisterEmail", "{ return true;}");

        $this->setRequestParameter('lgn_usr', 'testAddressTrimming@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd', 'Test@oxid-esales.com');
        $this->setRequestParameter('lgn_pwd2', 'Test@oxid-esales.com');
        $this->setRequestParameter('option', 3);

        $untrimmedInvoiceAddress = [
            'oxuser__oxfname'     => ' Simon ',
            'oxuser__oxlname'     => ' de la Serna ',
            'oxuser__oxstreetnr'  => 'nr',
            'oxuser__oxstreet'    => 'street ',
            'oxuser__oxzip'       => 'zip',
            'oxuser__oxcity'      => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
        ];

        $untrimmedDeliveryAddress = [
            'oxaddress__oxfname'     => ' Simon ',
            'oxaddress__oxlname'     => ' de la Serna ',
            'oxaddress__oxstreetnr'  => 'nr',
            'oxaddress__oxstreet'    => 'street ',
            'oxaddress__oxzip'       => 'zip',
            'oxaddress__oxcity'      => 'city',
            'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984',
        ];

        $trimmedAddressValues = [
            'Simon',
            'de la Serna',
        ];

        $this->setRequestParameter('invadr', $untrimmedInvoiceAddress);
        $this->setRequestParameter('deladr', $untrimmedDeliveryAddress);
        $this->setRequestParameter('blshowshipaddress', true);

        $parent = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class);

        $userComponent = $this->getMock(
            $this->getProxyClassName("oxcmp_user"),
            ['getParent']
        );

        $userComponent
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent));

        $this->assertEquals('payment?new_user=1&success=1', $userComponent->createUser());

        $userId = oxRegistry::getSession()->getVariable('usr');

        $user = oxNew('oxuser');
        $user->load($userId);

        $this->assertEquals(
            $trimmedAddressValues,
            [
                $user->oxuser__oxfname->value,
                $user->oxuser__oxlname->value,
            ]
        );

        $deliveryAddressId = oxRegistry::getSession()->getVariable('deladrid');

        $deliveryAddress = oxNew('oxaddress');
        $deliveryAddress->load($deliveryAddressId);

        $this->assertEquals(
            $trimmedAddressValues,
            [
                $deliveryAddress->oxaddress__oxfname->value,
                $deliveryAddress->oxaddress__oxlname->value,
            ]
        );
    }

    /**
     * @return array
     */
    public function providerDeleteShippingAddress()
    {
        return [
            ['oxdefaultadmin', false],
            ['differentUserId', true],
        ];
    }

    /**
     * @param string $userId
     * @param bool $isPossibleToLoadAddressAfterDeletion
     * @throws Exception
     *
     * @dataProvider providerDeleteShippingAddress
     */
    public function testDeleteShippingAddress($userId, $isPossibleToLoadAddressAfterDeletion)
    {
        $addressId = '_testAddressId';
        $this->addShippingAddress($userId, $addressId);
        $this->loadUserForShippingAddressDeletion();
        $this->makeSessionTokenToPassValidation();

        $this->setRequestParameter('oxaddressid', $addressId);
        $userComponent = oxNew(UserComponent::class);
        $userComponent->deleteShippingAddress();

        $this->assertSame($isPossibleToLoadAddressAfterDeletion, oxNew(Address::class)->load($addressId));
    }

    /**
     * @param string $userId
     * @param string $addressId
     * @throws Exception
     */
    private function addShippingAddress($userId, $addressId)
    {
        $address = oxNew(Address::class);
        $address->setId($addressId);
        $address->oxaddress__oxuserid = new \OxidEsales\Eshop\Core\Field($userId);
        $address->save();
    }

    private function loadUserForShippingAddressDeletion()
    {
        $user = oxNew(User::class);
        $user->load('oxdefaultadmin');
        $this->getSession()->setUser($user);
    }

    private function makeSessionTokenToPassValidation()
    {
        $this->setSessionParam('sess_stoken', 'testToken');
        $this->setRequestParameter('stoken', 'testToken');
    }
}
