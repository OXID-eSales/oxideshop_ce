<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxExceptionToDisplay;
use \oxbasket;
use \oxBasketHelper;
use \oxBasketReservation;
use OxidEsales\Eshop\Core\Registry;
use \oxUtilsServer;
use \oxUtilsObject;
use \oxSession;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxBasketHelper.php';

class UtilsServerHelper extends oxUtilsServer
{
    /**
     * $_COOKIE alternative for testing
     *
     * @var array
     */
    protected $_aCookieVars = [];

    public function setOxCookie($sVar, $sVal = "", $iExpire = 0, $sPath = '/', $sDomain = null, $blToSession = true, $blSecure = false, $blHttpOnly = true)
    {
        //unsetting cookie
        if (!isset($sVar) && !isset($sVal)) {
            $this->_aCookieVars = null;

            return;
        }

        $this->_aCookieVars[$sVar] = $sVal;
    }

    public function getOxCookie($sVar = null)
    {
        if (!$sVar) {
            return $this->_aCookieVars;
        }

        if ($this->_aCookieVars[$sVar] ?? null) {
            return $this->_aCookieVars[$sVar];
        }

        return null;
    }

    public function delOxCookie()
    {
        $this->_aCookieVars = null;
    }
}

class Unit_oxsessionTest_oxUtilsObject extends oxUtilsObject
{

    /**
     * Overriding original oxUtilsObject::generateUID()
     *
     * @return string
     */
    public function generateUid()
    {
        return "testsession";
    }
}

/**
 * oxSession child for testing
 */
class testSession extends oxSession
{

    /**
     * Keeps test session vars
     *
     * @var array
     */
    protected static $_aSessionVars = [];

    /**
     * Set session var for testing
     *
     * @param string $sVar
     * @param string $sVal
     */
    public static function setVar($sVar, $sVal)
    {
        self::$_aSessionVars[$sVar] = $sVal;
        //parent::setVar($sVar, $sVal);
    }

    /**
     * Gets session var for testing
     *
     * @param string $sVar
     *
     * @return string
     */
    public static function getVar($sVar)
    {
        return self::$_aSessionVars[$sVar] ?? oxRegistry::getSession()->getVariable($sVar);
    }

    /**
     * Deletes test var $sVar
     *
     * @param string $sVar
     */
    public static function deleteVar($sVar)
    {
        unset(self::$_aSessionVars[$sVar]);
    }

    /**
     * Checks protected $this->_blNewSession var
     *
     * @return bool
     */
    public function isNewSession()
    {
        return $this->_blNewSession;
    }

    /**
     * Initialize session data (calls php::session_start())
     *
     * @return null
     */
    protected function sessionStart()
    {
        //return @session_start();
    }

    /**
     * Ends the current session and store session data.
     *
     * @return null
     */
    public function freeze()
    {
    }
}

class SessionTest extends \OxidTestCase
{

    /**
     * Set session save path value if session.save_path value in php.ini is empty
     */
    public $sDefaultSessSavePath = '';

    /**
     * Internal oxSession instance
     *
     */
    public $oSession = null;

    /**
     * Original oxConfig instance
     *
     * @var object
     */
    protected $_oOriginalConfig = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oSession = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class);
    }

    protected function tearDown(): void
    {
        $this->oSession->freeze();
        parent::tearDown();
    }

    /**
     * Test case for oxSession::regenerateSessionId()
     *
     * @return null
     */
    public function testRegenerateSessionId()
    {
        $myConfig = $this->getConfig();

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ["getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->setVar('someVar1', true);
        $oSession->setVar('someVar2', 15);
        $oSession->setVar('actshop', 5);
        $oSession->setVar('lang', 3);
        $oSession->setVar('currency', 3);
        $oSession->setVar('language', 12);
        $oSession->setVar('tpllanguage', 12);

        $oSession->regenerateSessionId();

        $this->assertEquals("newSessionId", $oSession->getId());

        $this->assertEquals($oSession->getVar('someVar1'), true);
        $this->assertEquals($oSession->getVar('someVar2'), 15);
        $this->assertEquals($oSession->getVar('actshop'), 5);
        $this->assertEquals($oSession->getVar('lang'), 3);
        $this->assertEquals($oSession->getVar('currency'), 3);
        $this->assertEquals($oSession->getVar('language'), 12);
        $this->assertEquals($oSession->getVar('tpllanguage'), 12);

        $oSession->setVar('someVar1', null);
        $oSession->setVar('someVar2', null);
        $oSession->setVar('actshop', null);
        $oSession->setVar('lang', null);
        $oSession->setVar('currency', null);
        $oSession->setVar('tpllanguage', null);
    }

    /**
     * Test for oxSession::start()
     *
     * @return null
     */
    public function testStartWhenDebugisOnAndErrorMessageExpected()
    {
        $this->getConfig()->setConfigParam('iDebug', 1);
        $this->setRequestParameter("sid", "testSid");

        $this->getSession()->setVariable('sessionagent', 'oldone');

        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "isTrustedClientIp", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{return 'none';}");

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["allowSessionStart", "initNewSession", "setSessionId", "sessionStart"]);
        $oSession->expects($this->any())->method('allowSessionStart')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('initNewSession');
        $oSession->expects($this->any())->method('setSessionId');
        $oSession->expects($this->any())->method('sessionStart');
        $oSession->start();

        $aErrors = $this->getSession()->getVariable('Errors');

        $this->assertTrue(is_array($aErrors));
        $this->assertEquals(1, count($aErrors));

        $oExcp = unserialize(current($aErrors['default']));
        $this->assertNotNull($oExcp);
        $this->assertTrue($oExcp instanceof \OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay);
        $this->assertEquals("Different browser (oldone, none), creating new SID...<br>", $oExcp->getOxMessage());
    }

    public function testIsSidNeededPassingCustomUrl()
    {
        $sUrl = "someurl";

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["getConfig", 'getSessionUseCookies', 'isSessionStarted']);
        $oSession->expects($this->once())->method('getSessionUseCookies')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded($sUrl));
    }

    public function testIsSidNeededPassingCustomUrlChangeSsl()
    {
        $sUrl = "https://someurl";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isCurrentProtocol"]);
        $oConfig->expects($this->once())->method('isCurrentProtocol')->will($this->returnValue(false));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["getConfig", 'getSessionUseCookies', 'getCookieSid', 'isSessionStarted']);
        $oSession->expects($this->once())->method('getSessionUseCookies')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('getCookieSid')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded($sUrl));
    }

    public function testAllowSessionStartWhenSearchEngine()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return true;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = oxNew('oxSession');
        $this->assertFalse($oSession->allowSessionStart());
    }

    public function testAllowSessionStartIsAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isAdmin']);
        $oSession->expects($this->atLeastOnce())->method('isAdmin')->will($this->returnValue(true));
        $this->assertTrue($oSession->allowSessionStart());
    }

    public function testAllowSessionStartWhenSkipSessionParam()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        $this->setRequestParameter('skipSession', 1);

        $oSession = oxNew('oxSession');
        $this->assertFalse($oSession->allowSessionStart());
    }

    public function testAllowSessionStartSessionRequiredAction()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionRequiredAction']);
        $oSession->expects($this->atLeastOnce())->method('isSessionRequiredAction')->will($this->returnValue(true));
        $this->assertTrue($oSession->allowSessionStart());
    }

    public function testAllowSessionStartCookieIsFoundMustStart()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionRequiredAction']);
        $oSession->expects($this->any())->method('isSessionRequiredAction')->will($this->returnValue(false));
        $this->assertTrue($oSession->allowSessionStart());
    }

    public function testAllowSessionStartRequestContainsSidParameter()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->setRequestParameter('skipSession', 0);
        $this->setRequestParameter('sid', 'xxx');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionRequiredAction']);
        $oSession->expects($this->any())->method('isSessionRequiredAction')->will($this->returnValue(false));
        $this->assertTrue($oSession->allowSessionStart());
    }

    public function testProcessUrlSidIsNotNeeded()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSidNeeded']);
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(false));
        $this->assertEquals('sameurl', $oSession->processUrl('sameurl'));
    }

    public function testProcessUrlSidNeeded()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSidNeeded', 'sid']);
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('sid')->will($this->returnValue('sid=xxx'));
        $this->assertEquals('sameurl?sid=xxx&amp;', $oSession->processUrl('sameurl'));
    }

    public function testProcessUrlSidNeededButNotEgzisting()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSidNeeded', 'sid']);
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('sid')->will($this->returnValue(''));
        $this->assertEquals('sameurl', $oSession->processUrl('sameurl'));
    }

    public function testIsSidNeededForceSessionStart()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['forceSessionStart', 'isSessionStarted']);
        $oSession->expects($this->once())->method('forceSessionStart')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded());
    }

    public function testForceSessionStart_notSearchEngine()
    {
        $oSession = $this->getProxyClass('oxSession');

        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->assertFalse($oSession->forceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', true);
        $this->assertTrue($oSession->forceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->setRequestParameter('su', '123456');
        $this->assertTrue($oSession->forceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->setRequestParameter('su', '');
        $oSession->setNonPublicVar("_blForceNewSession", true);
        $this->assertTrue($oSession->forceSessionStart());
    }

    public function testForceSessionStart_isSearchEngine()
    {
        $oSession = $this->getProxyClass('oxSession');

        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return true;}");
        $this->getConfig()->setConfigParam('blForceSessionStart', true);
        $this->setRequestParameter('su', '123456');
        $oSession->setNonPublicVar("_blForceNewSession", true);

        $this->assertFalse($oSession->forceSessionStart());
    }

    public function testIsSidNeededWhenSearchEngine()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return true;}");

        $oSession = oxNew('oxsession');
        $this->assertFalse($oSession->isSidNeeded());
    }

    public function testIsSidNeededCookieFound()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");

        $oSession = oxNew('oxsession');
        $this->assertFalse($oSession->isSidNeeded());
    }

    public function testIsSidNeededWithSessionMarker()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $this->getSession()->setVariable('blSidNeeded', true);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionStarted']);
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded());
    }

    public function testIsSidNeededSpecialAction()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $this->getSession()->setVariable('blSidNeeded', false);
        $this->setRequestParameter('fnc', 'tobasket');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionStarted']);
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded());
        $this->assertTrue($oSession->getVariable('blSidNeeded'));
    }

    public function testIsSidNeededRegularPageViewNoSessionNeeded()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $this->getSession()->setVariable('blSidNeeded', false);

        $oSession = oxNew('oxsession');
        $this->assertFalse($oSession->isSidNeeded());
    }

    public function testIsSessionRequiredActionNoSpecialAction()
    {
        $this->setRequestParameter('fnc', 'nothingspecial');

        $oSession = oxNew('oxsession');
        $this->assertFalse($oSession->isSessionRequiredAction());
    }

    public function testIsSessionRequiredActionRequired()
    {
        $this->setRequestParameter('fnc', 'tobasket');

        $oSession = oxNew('oxsession');
        $this->assertTrue($oSession->isSessionRequiredAction());
    }

    /**
     * oxSession::start() test for admin login
     *
     */
    public function testStartAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', "getNewSessionId"]);
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));
        $this->assertNull($oSession->getId());
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->start();
        $this->assertNotNull($oSession->getId());
        $this->assertEquals($oSession->getName(), 'admin_sid');
        //$this->getConfig()->blAdmin = false;
    }

    /**
     * oxSession::start() test for non admin login
     *
     */
    public function testStartNonAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'allowSessionStart', "getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('allowSessionStart')->will($this->returnValue(true));
        $this->assertNull($oSession->getId());
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->start();
        $this->assertNotNull($oSession->getId());
        $this->assertEquals($oSession->getName(), 'sid');
    }

    /**
     * oxSession::start() test for non admin login
     *
     */
    public function testStartDoesNotGenerateSidIfNotNeeded()
    {
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['allowSessionStart']);
        $this->oSession->expects($this->any())->method('allowSessionStart')->will($this->returnValue(false));
        $this->assertNull($this->oSession->getId());
        $this->assertEquals($this->oSession->getName(), 'sid');
        $this->oSession->start();
        $this->assertNull($this->oSession->getId());
        $this->assertEquals($this->oSession->getName(), 'sid');
    }

    /**
     * oxSession::start() test forcing sid
     *
     */
    public function testStartSetsSidPriority()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin']);
        $this->oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        //set parameter
        $this->setRequestParameter('sid', 'testSid1');
        $this->oSession->start();
        $this->assertEquals($this->oSession->getId(), 'testSid1');

        //set cookie
        \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie('sid', 'testSid2');
        $this->getConfig()->setConfigParam('blSessionUseCookies', true);
        $this->oSession->start();
        $this->assertEquals($this->oSession->getId(), 'testSid2');

        //forcing sid (ususally for SSL<->nonSSL transitions)
        $this->setRequestParameter('force_sid', 'testSid3');
        $this->oSession->start();
        $this->assertEquals($this->oSession->getId(), 'testSid3');

        //reset params
        $this->setRequestParameter('sid', null);
        \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie('sid', null);
        $this->oSession->setVar('force_sid', null);
    }

    /**
     * oxSession::start() test forcing sid
     *
     */
    public function testStartSetsNewSid()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'initNewSession']);
        $this->oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $this->oSession->expects($this->any())->method('initNewSession');
        $this->oSession->setId('xxxx');
        $this->assertEquals('xxxx', $this->oSession->getId());
        $this->oSession->start();
        $this->assertNotEquals('xxxx', $this->oSession->getId());
    }

    /**
     * oxSession::start() cookies not available
     *
     */
    public function testStartCookiesNotAvailable()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'getCookieSid', 'isSwappedClient', 'allowSessionStart', "getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('getCookieSid')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSwappedClient')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('allowSessionStart')->will($this->returnValue(true));
        $this->setRequestParameter('force_sid', 'testSid3');
        $oSession->start();
        $this->assertNotEquals($oSession->getId(), 'testSid3');
    }

    /**
     * oxsession::allowSessionStart() test for normal case
     */
    public function testAllowSessionStartNormal()
    {
        $this->assertFalse($this->oSession->allowSessionStart());
    }

    public function testAllowSessionStartNormalForced()
    {
        $this->getConfig()->setConfigParam('blForceSessionStart', 1);
        $this->assertTrue($this->oSession->allowSessionStart());
    }

    /**
     * oxsession::allowSessionStart() test for search engines
     */
    public function testAllowSessionStartForSearchEngines()
    {
        oxRegistry::getUtils()->setSearchEngine(true);
        $this->assertFalse($this->oSession->allowSessionStart());
        oxRegistry::getUtils()->setSearchEngine(false);
    }

    /**
     * oxsession::allowSessionStart() test forcing skip
     */
    public function testAllowSessionStartForceSkip()
    {
        $this->setRequestParameter('skipSession', true);
        $this->assertFalse($this->oSession->allowSessionStart());
        $this->setRequestParameter('skipSession', false);
    }

    /**
     * oxsession::isSwappedClient() normal calse
     */
    public function testIsSwappedClientNormal()
    {
        $this->assertFalse($this->oSession->isSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() for search engines
     */
    public function testIsSwappedClientForSearchEngines()
    {
        oxRegistry::getUtils()->setSearchEngine(true);
        $this->assertFalse($this->oSession->isSwappedClient());
        oxRegistry::getUtils()->setSearchEngine(false);
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients
     */
    public function testIsSwappedClientAsDifferentUserAgent()
    {
        $oSubj = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkUserAgent']);
        $oSubj->expects($this->any())->method('checkUserAgent')->will($this->returnValue(true));
        $this->assertTrue($oSubj->isSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients with correct token
     */
    public function testIsSwappedClientAsDifferentUserAgentCorrectToken()
    {
        $this->setRequestParameter('rtoken', 'test1');

        $oSubj = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkUserAgent']);
        $oSubj->expects($this->any())->method('checkUserAgent')->will($this->returnValue(true));
        $oSubj->setVariable('_rtoken', 'test1');
        $this->assertFalse($oSubj->isSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients
     */
    public function testIsSwappedClientAsDifferentClientIfRemoteAccess()
    {
        $this->assertTrue($this->oSession->checkUserAgent('browser1', 'browser2'));
    }

    /**
     * oxsession::isSwappedClient() cookie check test is performed
     */
    public function testIsSwappedClientCookieCheck()
    {
        $myConfig = $this->getConfig();
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->assertFalse($this->oSession->checkCookies(null, null));
        $this->assertEquals("oxid", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('sid_key'));
        $this->assertFalse($this->oSession->checkCookies("oxid", null));
        $aSessCookSet = $this->oSession->getVar("sessioncookieisset");
        $this->assertEquals("ox_true", $aSessCookSet[$myConfig->getCurrentShopURL()]);
        $this->assertFalse($this->oSession->checkCookies("oxid", $aSessCookSet));
        $this->assertTrue($this->oSession->checkCookies(null, $aSessCookSet));
        $this->assertEquals("oxid", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('sid_key'));

        $this->getConfig()->setConfigParam('blSessionUseCookies', 1);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['checkCookies']);
        $oSession->expects($this->once())->method('checkCookies');
        $oSession->isSwappedClient();

        $this->getConfig()->setConfigParam('blSessionUseCookies', 0);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['checkCookies']);
        $oSession->expects($this->never())->method('checkCookies');
        $oSession->isSwappedClient();
    }

    /**
     * oxSession::_checkCookies() test case
     *
     * @return null
     */
    public function testCheckCookiesSsl()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl", "getSslShopUrl", "getShopUrl", "getConfigParam"]);
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getSslShopUrl')->will($this->returnValue("testsslurl"));

        $oSession = oxNew(\OxidEsales\Eshop\Core\Session::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertFalse($oSession->checkCookies(false, []));
    }

    /**
     * oxSession::_checkCookies() test case
     *
     * @return null
     */
    public function testCheckCookiesNoSsl()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl", "getSslShopUrl", "getShopUrl", "getConfigParam"]);
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue("testurl"));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('iDebug'))->will($this->returnValue(true));

        $oSession = oxNew(\OxidEsales\Eshop\Core\Session::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertTrue($oSession->checkCookies(false, ["testurl" => "ox_true"]));
    }

    /**
     * oxsession::intiNewSesssion() test
     */
    public function testInitNewSession()
    {
        $myConfig = $this->getConfig();

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ["getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->setVar('someVar1', true);
        $oSession->setVar('someVar2', 15);
        $oSession->setVar('actshop', 5);
        $oSession->setVar('lang', 3);
        $oSession->setVar('currency', 3);
        $oSession->setVar('language', 12);
        $oSession->setVar('tpllanguage', 12);

        $sOldSid = $oSession->getId();

        $oSession->initNewSession();

        //most sense is to perform this check
        //if session id was changed
        $this->assertNotEquals($sOldSid, $oSession->getId());

        //checking if new id is correct (md5($newid))
        $this->assertEquals("newSessionId", $oSession->getId());

        //$this->assertNotEquals($this->oSession->getVar('someVar1'), true);
        //$this->assertNotEquals($this->oSession->getVar('someVar2'), 15);
        $this->assertEquals($oSession->getVar('actshop'), 5);
        $this->assertEquals($oSession->getVar('lang'), 3);
        $this->assertEquals($oSession->getVar('currency'), 3);
        $this->assertEquals($oSession->getVar('language'), 12);
        $this->assertEquals($oSession->getVar('tpllanguage'), 12);

        $oSession->setVar('someVar1', null);
        $oSession->setVar('someVar2', null);
        $oSession->setVar('actshop', null);
        $oSession->setVar('lang', null);
        $oSession->setVar('currency', null);
        $oSession->setVar('tpllanguage', null);
    }

    /**
     * oxsession::intiNewSesssion() test
     */
    public function testInitNewSessionWithPersParams()
    {
        $myConfig = $this->getConfig();

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ["getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->setVar('someVar1', true);
        $oSession->setVar('someVar2', 15);
        $oSession->setVar('actshop', 5);
        $oSession->setVar('lang', 3);
        $oSession->setVar('currency', 3);
        $oSession->setVar('language', 12);
        $oSession->setVar('tpllanguage', 12);

        $sOldSid = $oSession->getId();

        $oSession->initNewSession();

        //most sense is to perform this check
        //if session id was changed
        $this->assertNotEquals($sOldSid, $oSession->getId());

        //checking if new id is correct (md5($newid))
        $this->assertEquals("newSessionId", $oSession->getId());

        $this->assertEquals($oSession->getVar('actshop'), 5);
        $this->assertEquals($oSession->getVar('lang'), 3);
        $this->assertEquals($oSession->getVar('currency'), 3);
        $this->assertEquals($oSession->getVar('language'), 12);
        $this->assertEquals($oSession->getVar('tpllanguage'), 12);

        $oSession->setVar('someVar1', null);
        $oSession->setVar('someVar2', null);
        $oSession->setVar('actshop', null);
        $oSession->setVar('lang', null);
        $oSession->setVar('currency', null);
        $oSession->setVar('tpllanguage', null);
    }

    /**
     * oxsession::setSessionId() test. Normal case
     */
    public function testSetSessionIdNormal()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->getConfig()->setConfigParam('blForceSessionStart', 0);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ["getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->setSessionId('testSid');

        $this->assertEquals($oSession->getId(), 'testSid');

        //reset session
        $oSession->initNewSession();
        $this->assertNotEquals('testSid', $oSession->getId());
    }

    public function testSetSessionIdSkipCookies()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{throw new Exception("may not! (set cookies while they are turned off)");}');

        $this->getConfig()->setConfigParam('blSessionUseCookies', 0);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, null);
        $oSession->setSessionId('test');
    }

    public function testSetSessionIdForced()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->getConfig()->setConfigParam('blForceSessionStart', 1);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ["getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->setSessionId('testSid');

        $this->assertEquals($oSession->getId(), 'testSid');
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($oSession->getName()), 'testSid');

        //reset session
        $oSession->InitNewSession();
        $this->assertNotEquals('testSid', $oSession->getId());
    }

    /**
     * oxsession::setSessionId() test. Admin
     */
    public function testSetSessionIdAdmin()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return 'testSid';}");

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'sessionStart', "getNewSessionId"]);
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('sessionStart')->will($this->returnValue(true));
        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        //session name is different..
        $this->assertEquals($oSession->getName(), 'admin_sid');
        $oSession->setSessionId('adminSessionId');

        //..but still eveything is set
        $this->assertEquals($oSession->getId(), 'adminSessionId');
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($this->oSession->getName()), 'testSid');

        //reset session
        $oSession->InitNewSession();
        $this->assertNotEquals($oSession->getId(), 'adminSessionId');
    }

    /**
     * oxsession::freeze() test
     */
    public function testFreeze()
    {
        //noting to test here as oxSession::freeze() includes only PHP session functionality
        //testing at least if this method exists by just calling it
        session_id("testSessId");
        $testSession = oxNew('oxSession');
        $testSession->freeze();
    }

    /**
     * $this->getSession()->setVariable() test
     */
    public function testSetHasGetVar()
    {
        //taking real session object
        $testSession = oxNew('oxSession');
        $testSession->setVariable('testVar', 'testVal');
        $this->assertTrue($testSession->hasVariable('testVar'));
        $this->assertEquals('testVal', $testSession->getVariable('testVar'));
    }

    /**
     * oxsession::sid() test normal case
     */
    public function testSidNormal()
    {
        $this->getConfig()->setConfigParam('blSessionUseCookies', false);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['getCookieSid', 'isAdmin']);
        $oSession->expects($this->any())->method('getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->setSessionId('testSid');
        $this->assertEquals('sid=testSid', $oSession->sid());

        $this->getConfig()->setConfigParam('blSessionUseCookies', true);
        $oSession->setSessionId('testSid');
        $this->assertEquals('', $oSession->sid());
    }

    /**
     * oxsession::sid() test normal case
     */
    public function testSidInAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['getCookieSid', 'isAdmin', 'getSessionChallengeToken', 'getShopUrlId']);
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->setSessionId('testSid');

        $this->assertEquals('stoken=stok', $oSession->sid());
    }

    /**
     * oxsession::sid() test normal case
     */
    public function testSidIfIdNotSetButSearchEngine()
    {
        $this->setConfigParam('blSessionUseCookies', false);
        $this->setConfigParam('aCacheViews', []);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['isSearchEngine']);
        $utils->expects($this->any())->method('isSearchEngine')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        /** @var testSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['getCookieSid', 'isAdmin']);
        $oSession->expects($this->any())->method('getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->setSessionId(null);
        $sSid = $oSession->sid();

        // update: shp adding functionality is also in oxUtilsUrl, where it belongs
        $this->assertEquals('', $sSid);
    }

    /**
     * oxsession::sid() test in amdin
     */
    public function testSidIsSearchEngine()
    {
        $this->setConfigParam('blSessionUseCookies', false);
        $this->setConfigParam('aCacheViews', []);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['isSearchEngine']);
        $utils->expects($this->any())->method('isSearchEngine')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['getCookieSid', 'isAdmin']);
        $oSession->expects($this->any())->method('getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->setSessionId('testSid');
        $sSid = $oSession->sid();

        // update: shp adding functionality is also in oxUtilsUrl, where it belongs
        $this->assertEquals('', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidIsAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'getSessionChallengeToken', 'isSidNeeded']);
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->setSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" /><input type="hidden" name="sid" value="testSid" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidIsAdminWithCookies()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'getSessionChallengeToken', 'isSidNeeded']);
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(false));
        $oSession->setSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidNotAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'getSessionChallengeToken', 'isSidNeeded']);
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->setSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" /><input type="hidden" name="sid" value="testSid" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidNotAdminWithCookies()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, ['isAdmin', 'getSessionChallengeToken', 'isSidNeeded']);
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(false));
        $oSession->setSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" />', $sSid);
    }

    /**
     * oxsession::getBasketName() test
     */
    public function testGetBasketNameblMallSharedBasket()
    {
        $this->assertEquals($this->oSession->getBasketName(), $this->getConfig()->getShopId() . '_basket');
    }

    /**
     * oxsession::getBasketName() test
     */
    public function testGetBasketName()
    {
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        $this->assertEquals('basket', $this->oSession->getBasketName());
    }

    /**
     *  oxsession::getBasket() not basket instance
     */
    public function testGetBasket_notBasketInstance()
    {
        $oClass = oxNew('__PHP_Incomplete_Class');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketName']);
        $oSession->expects($this->once())->method('getBasketName')->will($this->returnValue(serialize($oClass)));

        $oSessionBasket = $oSession->getBasket();
        $this->assertTrue($oSessionBasket instanceof \OxidEsales\EshopCommunity\Application\Model\Basket, "oSessionBasket is instance of oxbasket (found " . $oSessionBasket::class . ")");
    }

    /**
     *  oxsession::getBasket() wrong basket instance
     */
    public function testGetBasket_notWrongBasketInstance()
    {
        $oFakeBasket = oxNew('oxBasketHelper');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketName']);
        $oSession->expects($this->once())->method('getBasketName')->will($this->returnValue(serialize($oFakeBasket)));

        $oSessionBasket = $oSession->getBasket();
        $this->assertTrue($oSessionBasket instanceof \OxidEsales\EshopCommunity\Application\Model\Basket, "oSessionBasket is instance of oxBasket");
        $this->assertFalse($oSessionBasket instanceof oxBasketHelper, "oSessionBasket is not instance of oxBasketHelper");
    }

    /**
     *  oxsession::setBasket() test
     */
    public function testSetBasket_getBasket()
    {
        $oBasket = oxNew('oxBasket');
        $this->assertNotNull($oBasket);
        $this->oSession->setBasket($oBasket);

        $oSessionBasket = $this->oSession->getBasket();
        $this->assertEquals($oBasket, $oSessionBasket);
    }

    /**
     * oxsession::delBasket() test
     */
    public function testDelBasket()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketName']);
        $oSession->expects($this->once())->method('getBasketName')->will($this->returnValue('xxx'));
        $oSession->delBasket();
    }

    public function testGetRequestChallengeToken()
    {
        $oSession = oxNew('oxSession');
        $this->setRequestParameter('stoken', 'asd');
        $this->assertEquals('asd', $oSession->getRequestChallengeToken());
        $this->setRequestParameter('stoken', 'asd#asd$$');
        $this->assertEquals('asdasd', $oSession->getRequestChallengeToken());
    }

    public function testGetSessionChallengeToken()
    {
        $this->getSession()->setVariable('sess_stoken', '');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['initNewSessionChallenge']);
        $oSession->expects($this->once())->method('initNewSessionChallenge')->will($this->evalFunction('{oxRegistry::getSession()->setVariable("sess_stoken", "newtok");}'));
        $this->assertEquals('newtok', $oSession->getSessionChallengeToken());
        $this->getSession()->setVariable('sess_stoken', 'asd541)$#sdf');
        $this->assertEquals('asd541sdf', $oSession->getSessionChallengeToken());
    }



    public function testInitNewSessionChallenge()
    {
        $this->getSession()->setVariable('sess_stoken', '');
        $oSession = oxNew('oxSession');
        $this->assertEquals('', $this->getSession()->getVariable('sess_stoken'));
        $this->assertEquals('', $oSession->getRequestChallengeToken());

        $oSession->initNewSessionChallenge();
        $s1 = $this->getSession()->getVariable('sess_stoken');
        $this->assertNotEquals('', $s1);

        $oSession->initNewSessionChallenge();
        $s2 = $this->getSession()->getVariable('sess_stoken');
        $this->assertNotEquals('', $s2);
        $this->assertNotEquals($s1, $s2);
    }

    public function testInitNewSessionRecreatesChallengeToken()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['initNewSessionChallenge', "getNewSessionId", 'sessionStart']);
        $oSession->expects($this->any())->method('sessionStart')->will($this->returnValue(null));
        $oSession->expects($this->any())->method('getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->once())->method('initNewSessionChallenge');
        $oSession->initNewSession();
    }

    /**
     * test _getRequireSessionWithParams if no config val exists
     */
    public function testGetRequireSessionWithParamsNoConf()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oCfg->expects($this->once())->method('getConfigParam')
            ->with($this->equalTo('aRequireSessionWithParams'))
            ->will($this->returnValue(null));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $oSess = oxNew(\OxidEsales\Eshop\Core\Session::class);
        $this->assertEquals(
            ['cl'          =>
                ['register' => true, 'account'  => true], 'fnc'         =>
                ['tobasket'         => true, 'login_noredirect' => true, 'tocomparelist'    => true], '_artperpage' => true, 'ldtype'      => true, 'listorderby' => true],
            $oSess->getRequireSessionWithParams()
        );
    }

    /**
     * test _getRequireSessionWithParams if config val exists
     */
    public function testGetRequireSessionWithParamsWithConf()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oCfg->expects($this->once())->method('getConfigParam')
            ->with($this->equalTo('aRequireSessionWithParams'))
            ->will(
                $this->returnValue(
                    [
                        'cl'     => ['xxx' => 1],
                        // add new value inside param
                        'fnc'    => 1,
                        // override param to allow all values
                        '_param' => true,
                        // add new params
                        '_ddd'   => ['yyy' => 1],
                    ]
                )
            );
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $oSess = oxNew(\OxidEsales\Eshop\Core\Session::class);
        $this->assertEquals(
            ['cl'          =>
                ['xxx'      => 1, 'register' => true, 'account'  => true], 'fnc'         => 1, '_param'      => true, '_ddd'        => ['yyy' => 1], '_artperpage' => true, 'ldtype'      => true, 'listorderby' => true],
            $oSess->getRequireSessionWithParams()
        );
    }

    /**
     * check config array handling
     */
    public function testIsSessionRequiredAction()
    {
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getRequireSessionWithParams']);
        $oSess->expects($this->exactly(7))->method('getRequireSessionWithParams')
            ->will(
                $this->returnValue(
                    ['clx'  => true, 'fncx' => ['a1' => true, 's3' => 1]]
                )
            );
        $this->assertEquals(false, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('clx', '0');
        $this->assertEquals(true, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('fncx', '0');
        $this->assertEquals(true, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('clx', null);
        $this->assertEquals(false, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('fncx', 'a1');
        $this->assertEquals(true, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('fncx', 'a3');
        $this->assertEquals(false, $oSess->isSessionRequiredAction());
        $this->setRequestParameter('fncx', 's3');
        $this->assertEquals(true, $oSess->isSessionRequiredAction());
    }

    /**
     * check if forces session on POST request
     */
    public function testIsSessionRequiredActionOnPost()
    {
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getRequireSessionWithParams']);
        $oSess->expects($this->exactly(2))->method('getRequireSessionWithParams')
            ->will(
                $this->returnValue(
                    []
                )
            );

        $sInitial = $_SERVER['REQUEST_METHOD'];
        try {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $this->assertEquals(false, $oSess->isSessionRequiredAction());
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $this->assertEquals(true, $oSess->isSessionRequiredAction());
        } catch (Exception $e) {
        }
        $_SERVER['REQUEST_METHOD'] = $sInitial;
        if ($e) {
            throw $e;
        };
    }

    public function testGetRemoteAccessToken()
    {
        $oSubj = oxNew('oxSession');
        $sTestToken = $oSubj->getRemoteAccessToken();
        $this->assertEquals(8, strlen((string) $sTestToken));
    }

    public function testGetRemoteAccessTokenNotGenerated()
    {
        $oSubj = oxNew('oxSession');
        $oSubj->deleteVariable('_rtoken');
        $sTestToken = $oSubj->getRemoteAccessToken(false);

        $this->assertNull($sTestToken);
        //generating one
        $oSubj->getRemoteAccessToken();
        $sTestToken = $oSubj->getRemoteAccessToken(false);
        //expecting real tokent
        $this->assertEquals(8, strlen((string) $sTestToken));
    }

    public function testGetRemoteAccessTokenTwice()
    {
        $oSubj = oxNew('oxSession');
        $oSubj->deleteVariable('_rtoken');
        $sToken1 = $oSubj->getRemoteAccessToken();
        $sToken2 = $oSubj->getRemoteAccessToken();

        $this->assertEquals($sToken1, $sToken2);
        $this->assertEquals(8, strlen((string) $sToken2));
    }

    public function testIsRemoteAccessTokenValid()
    {
        $this->setRequestParameter('rtoken', 'test1');

        $oSubj = $this->getProxyClass('oxSession');
        $oSubj->setVariable('_rtoken', 'test1');
        $this->assertTrue($oSubj->isValidRemoteAccessToken());
    }

    /**
     * Test handling of supplying an array instead of a string for rtoken.
     */
    public function DISABLED_testIsRemoteAccessTokenValidArrayRequestParameter()
    {
        //Suppress all error reporting on purpose for this test
        $originalErrorReportingLevel =  error_reporting(0);
        try {
            $this->setRequestParam('rtoken', [1]);

            $session = $this->getProxyClass('oxSession');
            $session->setVariable('_rtoken', 'test1');
            $this->assertFalse($session->isValidRemoteAccessToken());
        } catch (\Throwable $throwable) {
            throw $throwable;
        } finally {
            error_reporting($originalErrorReportingLevel);
        }
    }

    public function testIsTokenValidNot()
    {
        $this->setRequestParameter('stoken', 'test1');

        $oSubj = $this->getProxyClass('oxSession');
        $oSubj->setVariable('_stoken', 'test2');
        $this->assertFalse($oSubj->isValidRemoteAccessToken());
    }

    public function testGetBasketReservations()
    {
        $this->assertTrue(oxRegistry::getSession()->getBasketReservations() instanceof \OxidEsales\EshopCommunity\Application\Model\BasketReservation);
        // test cache
        $this->assertSame(oxRegistry::getSession()->getBasketReservations(), oxRegistry::getSession()->getBasketReservations());
    }

    public function testSetForceNewSession()
    {
        $oSubj = $this->getProxyClass('oxSession');
        $this->assertFalse($oSubj->getNonPublicVar("_blForceNewSession"));

        $oSubj->setForceNewSession();
        $this->assertTrue($oSubj->getNonPublicVar("_blForceNewSession"));
    }

    public function testIsActualSidInCookiePossitive()
    {
        $sOriginalVal = $_COOKIE["sid"];
        $_COOKIE["sid"] = "testIdDifferent";
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->any())->method('getId')->will($this->returnValue('testId'));

        $blRes = $oSession->isActualSidInCookie();

        $_COOKIE["sid"] = $sOriginalVal;
        $this->assertFalse($blRes);
    }

    public function testIsActualSidInCookieNegative()
    {
        $sOriginalVal = $_COOKIE["sid"];
        $_COOKIE["sid"] = "testId";
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->any())->method('getId')->will($this->returnValue('testId'));

        $blRes = $oSession->isActualSidInCookie();

        $_COOKIE["sid"] = $sOriginalVal;

        $this->assertTrue($blRes);
    }

    public function testIsActualSidInCookieNotSet()
    {
        $sOriginalVal = $_COOKIE["sid"];
        unset($_COOKIE["sid"]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->any())->method('getId')->will($this->returnValue('testId'));

        $blRes = $oSession->isActualSidInCookie();

        $_COOKIE["sid"] = $sOriginalVal;

        $this->assertFalse($blRes);
    }

    public function testIfSetHeader()
    {
        $oSession = oxNew('oxsession');
        $this->assertTrue($oSession->needToSetHeaders());
    }

    public function testInitNewSessionUnsetsSessionVariables(): void
    {
        $session = Registry::getSession();

        $session->setVariable('testVariable', 'value');
        Registry::getSession()->initNewSession();

        $this->assertNull($session->getVariable('testVariable'));
    }
}
