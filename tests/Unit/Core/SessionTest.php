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
    protected $_aCookieVars = array();

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

        if ($this->_aCookieVars[$sVar]) {
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
    protected static $_aSessionVars = array();

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
        if (isset(self::$_aSessionVars[$sVar])) {
            return self::$_aSessionVars[$sVar];
        }

        return oxRegistry::getSession()->getVariable($sVar);
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
    protected function _sessionStart()
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


/**
 * Testing oxsession class
 */
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

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        //creating new instance
        $this->oSession = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        //removing oxUtils module
        oxRemClassModule('testUtils');
        oxRemClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class);
        oxRemClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\Unit_oxsessionTest_oxUtilsObject::class);

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

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array("_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->setVar('someVar1', true);
        $oSession->setVar('someVar2', 15);
        $oSession->setVar('actshop', 5);
        $oSession->setVar('lang', 3);
        $oSession->setVar('currency', 3);
        $oSession->setVar('language', 12);
        $oSession->setVar('tpllanguage', 12);

        $sOldSid = $oSession->getId();

        $oSession->regenerateSessionId();

        //most sense is to perform this check
        //if session id was changed
        $this->assertNotEquals($sOldSid, $oSession->getId());

        //checking if new id is correct (md5($newid))
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
        $oSession->setVar('language', $myConfig->sDefaultLang);
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

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("_allowSessionStart", "initNewSession", "_setSessionId", "_sessionStart"));
        $oSession->expects($this->any())->method('_allowSessionStart')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('initNewSession');
        $oSession->expects($this->any())->method('_setSessionId');
        $oSession->expects($this->any())->method('_sessionStart');
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

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getConfig", '_getSessionUseCookies', 'isSessionStarted'));
        $oSession->expects($this->once())->method('_getSessionUseCookies')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded($sUrl));
    }

    public function testIsSidNeededPassingCustomUrlChangeSsl()
    {
        $sUrl = "https://someurl";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isCurrentProtocol"));
        $oConfig->expects($this->once())->method('isCurrentProtocol')->will($this->returnValue(false));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getConfig", '_getSessionUseCookies', '_getCookieSid', 'isSessionStarted'));
        $oSession->expects($this->once())->method('_getSessionUseCookies')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('_getCookieSid')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded($sUrl));
    }

    public function testAllowSessionStartWhenSearchEngine()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return true;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = oxNew('oxSession');
        $this->assertFalse($oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartIsAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isAdmin'));
        $oSession->expects($this->atLeastOnce())->method('isAdmin')->will($this->returnValue(true));
        $this->assertTrue($oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartWhenSkipSessionParam()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        $this->setRequestParameter('skipSession', 1);

        $oSession = oxNew('oxSession');
        $this->assertFalse($oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartSessionRequiredAction()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_isSessionRequiredAction'));
        $oSession->expects($this->atLeastOnce())->method('_isSessionRequiredAction')->will($this->returnValue(true));
        $this->assertTrue($oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartCookieIsFoundMustStart()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->setRequestParameter('skipSession', 0);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_isSessionRequiredAction'));
        $oSession->expects($this->any())->method('_isSessionRequiredAction')->will($this->returnValue(false));
        $this->assertTrue($oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartRequestContainsSidParameter()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->setRequestParameter('skipSession', 0);
        $this->setRequestParameter('sid', 'xxx');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_isSessionRequiredAction'));
        $oSession->expects($this->any())->method('_isSessionRequiredAction')->will($this->returnValue(false));
        $this->assertTrue($oSession->UNITallowSessionStart());
    }

    public function testProcessUrlSidIsNotNeeded()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isSidNeeded'));
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(false));
        $this->assertEquals('sameurl', $oSession->processUrl('sameurl'));
    }

    public function testProcessUrlSidNeeded()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isSidNeeded', 'sid'));
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('sid')->will($this->returnValue('sid=xxx'));
        $this->assertEquals('sameurl?sid=xxx&amp;', $oSession->processUrl('sameurl'));
    }

    public function testProcessUrlSidNeededButNotEgzisting()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isSidNeeded', 'sid'));
        $oSession->expects($this->once())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->expects($this->once())->method('sid')->will($this->returnValue(''));
        $this->assertEquals('sameurl', $oSession->processUrl('sameurl'));
    }

    public function testIsSidNeededForceSessionStart()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_forceSessionStart', 'isSessionStarted'));
        $oSession->expects($this->once())->method('_forceSessionStart')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded());
    }

    public function testForceSessionStart_notSearchEngine()
    {
        $oSession = $this->getProxyClass('oxSession');

        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->assertFalse($oSession->UNITforceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', true);
        $this->assertTrue($oSession->UNITforceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->setRequestParameter('su', '123456');
        $this->assertTrue($oSession->UNITforceSessionStart());

        $this->getConfig()->setConfigParam('blForceSessionStart', false);
        $this->setRequestParameter('su', '');
        $oSession->setNonPublicVar("_blForceNewSession", true);
        $this->assertTrue($oSession->UNITforceSessionStart());
    }

    public function testForceSessionStart_isSearchEngine()
    {
        $oSession = $this->getProxyClass('oxSession');

        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return true;}");
        $this->getConfig()->setConfigParam('blForceSessionStart', true);
        $this->setRequestParameter('su', '123456');
        $oSession->setNonPublicVar("_blForceNewSession", true);

        $this->assertFalse($oSession->UNITforceSessionStart());
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

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isSessionStarted'));
        $oSession->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));
        $this->assertTrue($oSession->isSidNeeded());
    }

    public function testIsSidNeededSpecialAction()
    {
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return false;}");

        $this->getSession()->setVariable('blSidNeeded', false);
        $this->setRequestParameter('fnc', 'tobasket');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isSessionStarted'));
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
        $this->assertFalse($oSession->UNITisSessionRequiredAction());
    }

    public function testIsSessionRequiredActionRequired()
    {
        $this->setRequestParameter('fnc', 'tobasket');

        $oSession = oxNew('oxsession');
        $this->assertTrue($oSession->UNITisSessionRequiredAction());
    }

    /**
     * oxSession::start() test for admin login
     *
     */
    public function testStartAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', "_getNewSessionId"));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));
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
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', '_allowSessionStart', "_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('_allowSessionStart')->will($this->returnValue(true));
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
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_allowSessionStart'));
        $this->oSession->expects($this->any())->method('_allowSessionStart')->will($this->returnValue(false));
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
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin'));
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
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', 'initNewSession'));
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
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', '_getCookieSid', '_isSwappedClient', '_allowSessionStart', "_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('_getCookieSid')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('_isSwappedClient')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('_allowSessionStart')->will($this->returnValue(true));
        $this->setRequestParameter('force_sid', 'testSid3');
        $oSession->start();
        $this->assertNotEquals($oSession->getId(), 'testSid3');
    }

    /**
     * oxsession::allowSessionStart() test for normal case
     */
    public function testAllowSessionStartNormal()
    {
        $this->assertFalse($this->oSession->UNITallowSessionStart());
    }

    public function testAllowSessionStartNormalForced()
    {
        $this->getConfig()->setConfigParam('blForceSessionStart', 1);
        $this->assertTrue($this->oSession->UNITallowSessionStart());
    }

    /**
     * oxsession::allowSessionStart() test for search engines
     */
    public function testAllowSessionStartForSearchEngines()
    {
        oxRegistry::getUtils()->setSearchEngine(true);
        $this->assertFalse($this->oSession->UNITallowSessionStart());
        oxRegistry::getUtils()->setSearchEngine(false);
    }

    /**
     * oxsession::allowSessionStart() test forcing skip
     */
    public function testAllowSessionStartForceSkip()
    {
        $this->setRequestParameter('skipSession', true);
        $this->assertFalse($this->oSession->UNITallowSessionStart());
        $this->setRequestParameter('skipSession', false);
    }

    /**
     * oxsession::isSwappedClient() normal calse
     */
    public function testIsSwappedClientNormal()
    {
        $this->assertFalse($this->oSession->UNITisSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() for search engines
     */
    public function testIsSwappedClientForSearchEngines()
    {
        oxRegistry::getUtils()->setSearchEngine(true);
        $this->assertFalse($this->oSession->UNITisSwappedClient());
        oxRegistry::getUtils()->setSearchEngine(false);
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients
     */
    public function testIsSwappedClientAsDifferentUserAgent()
    {
        $oSubj = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_checkUserAgent'));
        $oSubj->expects($this->any())->method('_checkUserAgent')->will($this->returnValue(true));
        $this->assertTrue($oSubj->UNITisSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients with correct token
     */
    public function testIsSwappedClientAsDifferentUserAgentCorrectToken()
    {
        $this->setRequestParameter('rtoken', 'test1');

        $oSubj = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_checkUserAgent'));
        $oSubj->expects($this->any())->method('_checkUserAgent')->will($this->returnValue(true));
        $oSubj->setVariable('_rtoken', 'test1');
        $this->assertFalse($oSubj->UNITisSwappedClient());
    }

    /**
     * oxsession::isSwappedClient() as for differnet clients
     */
    public function testIsSwappedClientAsDifferentClientIfRemoteAccess()
    {
        $this->assertTrue($this->oSession->UNITcheckUserAgent('browser1', 'browser2'));
    }

    /**
     * oxsession::isSwappedClient() cookie check test is performed
     */
    public function testIsSwappedClientCookieCheck()
    {
        $myConfig = $this->getConfig();
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->assertFalse($this->oSession->UNITcheckCookies(null, null));
        $this->assertEquals("oxid", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('sid_key'));
        $this->assertFalse($this->oSession->UNITcheckCookies("oxid", null));
        $aSessCookSet = $this->oSession->getVar("sessioncookieisset");
        $this->assertEquals("ox_true", $aSessCookSet[$myConfig->getCurrentShopURL()]);
        $this->assertFalse($this->oSession->UNITcheckCookies("oxid", $aSessCookSet));
        $this->assertTrue($this->oSession->UNITcheckCookies(null, $aSessCookSet));
        $this->assertEquals("oxid", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('sid_key'));

        $this->getConfig()->setConfigParam('blSessionUseCookies', 1);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_checkCookies'));
        $oSession->expects($this->once())->method('_checkCookies');
        $oSession->UNITisSwappedClient();

        $this->getConfig()->setConfigParam('blSessionUseCookies', 0);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_checkCookies'));
        $oSession->expects($this->never())->method('_checkCookies');
        $oSession->UNITisSwappedClient();
    }

    /**
     * oxSession::_checkCookies() test case
     *
     * @return null
     */
    public function testCheckCookiesSsl()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isSsl", "getSslShopUrl", "getShopUrl", "getConfigParam"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->once())->method('getSslShopUrl')->will($this->returnValue("testsslurl"));
        $oConfig->expects($this->never())->method('getShopUrl');
        $oConfig->expects($this->never())->method('getConfigParam');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getConfig"));
        $oSession->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertFalse($oSession->UNITcheckCookies(false, array()));
    }

    /**
     * oxSession::_checkCookies() test case
     *
     * @return null
     */
    public function testCheckCookiesNoSsl()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isSsl", "getSslShopUrl", "getShopUrl", "getConfigParam"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue("testurl"));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('iDebug'))->will($this->returnValue(true));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getConfig"));
        $oSession->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertTrue($oSession->UNITcheckCookies(false, array("testurl" => "ox_true")));
    }

    /**
     * oxsession::intiNewSesssion() test
     */
    public function testInitNewSession()
    {
        $myConfig = $this->getConfig();

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array("_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

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
        $oSession->setVar('language', $myConfig->sDefaultLang);
        $oSession->setVar('tpllanguage', null);
    }

    /**
     * oxsession::intiNewSesssion() test
     */
    public function testInitNewSessionWithPersParams()
    {
        $myConfig = $this->getConfig();

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array("_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

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
        $oSession->setVar('language', $myConfig->sDefaultLang);
        $oSession->setVar('tpllanguage', null);
    }

    /**
     * oxsession::setSessionId() test. Normal case
     */
    public function testSetSessionIdNormal()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return true;}");
        $this->getConfig()->setConfigParam('blForceSessionStart', 0);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array("_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->UNITsetSessionId('testSid');

        $this->assertEquals($oSession->getId(), 'testSid');
        $this->assertTrue($oSession->isNewSession());

        //reset session
        $oSession->initNewSession();
        $this->assertNotEquals('testSid', $oSession->getId());
    }

    public function testSetSessionIdSkipCookies()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{throw new Exception("may not! (set cookies while they are turned off)");}');

        $this->getConfig()->setConfigParam('blSessionUseCookies', 0);
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_allowSessionStart'));
        $oSession->expects($this->once())->method('_allowSessionStart')->will($this->returnValue(false));
        $oSession->UNITsetSessionId('test');

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_allowSessionStart'));
        $oSession->expects($this->once())->method('_allowSessionStart')->will($this->returnValue(true));
        $oSession->UNITsetSessionId('test');
    }

    public function testSetSessionIdForced()
    {
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Core\UtilsServerHelper::class, 'oxUtilsServer');
        $this->getConfig()->setConfigParam('blForceSessionStart', 1);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array("_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->UNITsetSessionId('testSid');

        $this->assertEquals($oSession->getId(), 'testSid');
        $this->assertTrue($oSession->isNewSession());
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

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', '_sessionStart', "_getNewSessionId"));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));

        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('_sessionStart')->will($this->returnValue(true));
        $this->assertFalse($oSession->isNewSession());

        $oSession->start();
        //session name is different..
        $this->assertEquals($oSession->getName(), 'admin_sid');
        $oSession->UNITsetSessionId('testSid');

        //..but still eveything is set
        $this->assertEquals($oSession->getId(), 'testSid');
        $this->assertTrue($oSession->isNewSession());
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($this->oSession->getName()), 'testSid');

        //reset session
        $oSession->InitNewSession();
        $this->assertNotEquals($oSession->getId(), 'testSid');
    }

    /**
     * oxsession::setSessionId() test. For search engines.
     */
    public function testSetSessionIdSearchEngines()
    {
        oxRegistry::getUtils()->setSearchEngine(true);

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("_getNewSessionId", "_allowSessionStart"));
        $oSession->expects($this->any())->method('_getNewSessionId');
        $oSession->expects($this->any())->method('_allowSessionStart')->will($this->returnValue(true));

        $this->assertFalse($oSession->isNewSession());

        $oSession->start();

        $this->assertEquals($oSession->getName(), 'sid');
        $oSession->UNITsetSessionId('testSid');

        $this->assertEquals($oSession->getId(), 'testSid');
        $this->assertTrue($oSession->isNewSession());

        //have no cookie as search engine
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($oSession->getName()), null);

        //reset session
        $oSession->initNewSession();
        $this->assertNotEquals($oSession->getId(), 'testSid');

        //teardown
        oxRegistry::getUtils()->setSearchEngine(false);
    }

    /**
     * oxsession::checkMandatoryCookieSupport() test. Normal case. not critical action.
     */
    /*function testCheckMandatoryCookieSupportNormal()
    {
        $this->getConfig()->setConfigParam( 'blSessionEnforceCookies', false );
        $this->assertTrue($this->oSession->UNITcheckMandatoryCookieSupport( "account", "tobasket"));
    }*/

    /**
     * oxsession::checkMandatoryCookieSupport() test in critical action when cookies are supported
     */
    /*function testCheckMandatoryCookieSupportCookiesSupported()
    {
        $this->getConfig()->setConfigParam( 'blSessionEnforceCookies', true );
        $this->assertFalse($this->oSession->UNITcheckMandatoryCookieSupport( 'register', '' ));
        $this->assertFalse($this->oSession->UNITcheckMandatoryCookieSupport( 'account', ''));
        $this->assertFalse($this->oSession->UNITcheckMandatoryCookieSupport( 'alist', 'tobasket'));
        $this->assertFalse($this->oSession->UNITcheckMandatoryCookieSupport( 'details', 'login_noredirect'));
    }*/

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
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_getCookieSid', 'isAdmin'));
        $oSession->expects($this->any())->method('_getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->UNITsetSessionId('testSid');
        $this->assertEquals('sid=testSid', $oSession->sid());

        $this->getConfig()->setConfigParam('blSessionUseCookies', true);
        $oSession->UNITsetSessionId('testSid');
        $this->assertEquals('', $oSession->sid());
    }

    /**
     * oxsession::sid() test normal case
     */
    public function testSidInAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_getCookieSid', 'isAdmin', 'getSessionChallengeToken', 'getShopUrlId'));
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('_getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->UNITsetSessionId('testSid');

        $this->assertEquals('stoken=stok', $oSession->sid());
    }

    /**
     * oxsession::sid() test normal case
     */
    public function testSidIfIdNotSetButSearchEngine()
    {
        $this->setConfigParam('blSessionUseCookies', false);
        $this->setConfigParam('aCacheViews', array());

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('isSearchEngine'));
        $utils->expects($this->any())->method('isSearchEngine')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        /** @var testSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_getCookieSid', 'isAdmin'));
        $oSession->expects($this->any())->method('_getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->UNITsetSessionId(null);
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
        $this->setConfigParam('aCacheViews', array());

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('isSearchEngine'));
        $utils->expects($this->any())->method('isSearchEngine')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('_getCookieSid', 'isAdmin'));
        $oSession->expects($this->any())->method('_getCookieSid')->will($this->returnValue('admin_sid'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->UNITsetSessionId('testSid');
        $sSid = $oSession->sid();

        // update: shp adding functionality is also in oxUtilsUrl, where it belongs
        $this->assertEquals('', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidIsAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', 'getSessionChallengeToken', 'isSidNeeded'));
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->UNITsetSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" /><input type="hidden" name="force_sid" value="testSid" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidIsAdminWithCookies()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', 'getSessionChallengeToken', 'isSidNeeded'));
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(false));
        $oSession->UNITsetSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidNotAdmin()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', 'getSessionChallengeToken', 'isSidNeeded'));
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->UNITsetSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" /><input type="hidden" name="force_sid" value="testSid" />', $sSid);
    }

    /**
     * oxsession::hiddenSid() test
     */
    public function testHiddenSidNotAdminWithCookies()
    {
        $oSession = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\testSession::class, array('isAdmin', 'getSessionChallengeToken', 'isSidNeeded'));
        $oSession->expects($this->any())->method('getSessionChallengeToken')->will($this->returnValue('stok'));
        $oSession->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(false));
        $oSession->UNITsetSessionId('testSid');
        $sSid = $oSession->hiddenSid();
        $this->assertEquals('<input type="hidden" name="stoken" value="stok" />', $sSid);
    }

    /**
     * oxsession::getBasketName() test
     */
    public function testGetBasketNameblMallSharedBasket()
    {
        $this->assertEquals($this->oSession->UNITgetBasketName(), $this->getConfig()->getShopId() . '_basket');
    }

    /**
     * oxsession::getBasketName() test
     */
    public function testGetBasketName()
    {
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        $this->assertEquals('basket', $this->oSession->UNITgetBasketName());
    }

    /**
     *  oxsession::getBasket() not basket instance
     */
    public function testGetBasket_notBasketInstance()
    {
        $oClass = oxNew('__PHP_Incomplete_Class');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_getBasketName'));
        $oSession->expects($this->once())->method('_getBasketName')->will($this->returnValue(serialize($oClass)));

        $oSessionBasket = $oSession->getBasket();
        $this->assertTrue($oSessionBasket instanceof \OxidEsales\EshopCommunity\Application\Model\Basket, "oSessionBasket is instance of oxbasket (found " . get_class($oSessionBasket) . ")");
    }

    /**
     *  oxsession::getBasket() wrong basket instance
     */
    public function testGetBasket_notWrongBasketInstance()
    {
        $oFakeBasket = oxNew('oxBasketHelper');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_getBasketName'));
        $oSession->expects($this->once())->method('_getBasketName')->will($this->returnValue(serialize($oFakeBasket)));

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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_getBasketName'));
        $oSession->expects($this->once())->method('_getBasketName')->will($this->returnValue('xxx'));
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_initNewSessionChallenge'));
        $oSession->expects($this->once())->method('_initNewSessionChallenge')->will($this->evalFunction('{oxRegistry::getSession()->setVariable("sess_stoken", "newtok");}'));
        $this->assertEquals('newtok', $oSession->getSessionChallengeToken());
        $this->getSession()->setVariable('sess_stoken', 'asd541)$#sdf');
        $this->assertEquals('asd541sdf', $oSession->getSessionChallengeToken());
    }

    public function testCheckSessionChallenge()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getSessionChallengeToken', 'getRequestChallengeToken'));
        $oSession->expects($this->once())->method('getSessionChallengeToken')->will($this->returnValue(''));
        $oSession->expects($this->never())->method('getRequestChallengeToken')->will($this->returnValue(''));
        $this->assertEquals(false, $oSession->checkSessionChallenge());

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getSessionChallengeToken', 'getRequestChallengeToken'));
        $oSession->expects($this->once())->method('getSessionChallengeToken')->will($this->returnValue('aa'));
        $oSession->expects($this->once())->method('getRequestChallengeToken')->will($this->returnValue('aad'));
        $this->assertEquals(false, $oSession->checkSessionChallenge());

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getSessionChallengeToken', 'getRequestChallengeToken'));
        $oSession->expects($this->once())->method('getSessionChallengeToken')->will($this->returnValue('aa'));
        $oSession->expects($this->once())->method('getRequestChallengeToken')->will($this->returnValue('aa'));
        $this->assertEquals(true, $oSession->checkSessionChallenge());
    }

    public function testInitNewSessionChallenge()
    {
        $this->getSession()->setVariable('sess_stoken', '');
        $oSession = oxNew('oxSession');
        $this->assertEquals('', $this->getSession()->getVariable('sess_stoken'));
        $this->assertEquals('', $oSession->getRequestChallengeToken());

        $oSession->UNITinitNewSessionChallenge();
        $s1 = $this->getSession()->getVariable('sess_stoken');
        $this->assertNotEquals('', $s1);

        $oSession->UNITinitNewSessionChallenge();
        $s2 = $this->getSession()->getVariable('sess_stoken');
        $this->assertNotEquals('', $s2);
        $this->assertNotEquals($s1, $s2);
    }

    public function testInitNewSessionRecreatesChallengeToken()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_initNewSessionChallenge', "_getNewSessionId", '_sessionStart'));
        $oSession->expects($this->any())->method('_sessionStart')->will($this->returnValue(null));
        $oSession->expects($this->any())->method('_getNewSessionId')->will($this->returnValue("newSessionId"));
        $oSession->expects($this->once())->method('_initNewSessionChallenge');
        $oSession->initNewSession();
    }

    /**
     * test _getRequireSessionWithParams if no config val exists
     */
    public function testGetRequireSessionWithParamsNoConf()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')
            ->with($this->equalTo('aRequireSessionWithParams'))
            ->will($this->returnValue(null));
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getConfig'));
        $oSess->expects($this->once())->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals(
            array(
                 'cl'          =>
                     array(
                         'register' => true,
                         'account'  => true,
                     ),
                 'fnc'         =>
                     array(
                         'tobasket'         => true,
                         'login_noredirect' => true,
                         'tocomparelist'    => true,
                     ),
                 '_artperpage' => true,
                 'ldtype'      => true,
                 'listorderby' => true,
            ),
            $oSess->UNITgetRequireSessionWithParams()
        );
    }

    /**
     * test _getRequireSessionWithParams if config val exists
     */
    public function testGetRequireSessionWithParamsWithConf()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')
            ->with($this->equalTo('aRequireSessionWithParams'))
            ->will(
                $this->returnValue(
                    array(
                         'cl'     => array('xxx' => 1), // add new value inside param
                         'fnc'    => 1, // override param to allow all values
                         '_param' => true, // add new params
                         '_ddd'   => array('yyy' => 1),
                    )
                )
            );
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getConfig'));
        $oSess->expects($this->once())->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals(
            array(
                 'cl'          =>
                     array(
                         'xxx'      => 1,
                         'register' => true,
                         'account'  => true,
                     ),
                 'fnc'         => 1,
                 '_param'      => true,
                 '_ddd'        => array('yyy' => 1),
                 '_artperpage' => true,
                 'ldtype'      => true,
                 'listorderby' => true,
            ),
            $oSess->UNITgetRequireSessionWithParams()
        );
    }

    /**
     * check config array handling
     */
    public function testIsSessionRequiredAction()
    {
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_getRequireSessionWithParams'));
        $oSess->expects($this->exactly(7))->method('_getRequireSessionWithParams')
            ->will(
                $this->returnValue(
                    array(
                         'clx'  => true,
                         'fncx' => array('a1' => true, 's3' => 1),
                    )
                )
            );
        $this->assertEquals(false, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('clx', '0');
        $this->assertEquals(true, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('fncx', '0');
        $this->assertEquals(true, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('clx', null);
        $this->assertEquals(false, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('fncx', 'a1');
        $this->assertEquals(true, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('fncx', 'a3');
        $this->assertEquals(false, $oSess->UNITisSessionRequiredAction());
        $this->setRequestParameter('fncx', 's3');
        $this->assertEquals(true, $oSess->UNITisSessionRequiredAction());
    }

    /**
     * check if forces session on POST request
     */
    public function testIsSessionRequiredActionOnPost()
    {
        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('_getRequireSessionWithParams'));
        $oSess->expects($this->exactly(2))->method('_getRequireSessionWithParams')
            ->will(
                $this->returnValue(
                    array()
                )
            );

        $sInitial = $_SERVER['REQUEST_METHOD'];
        try {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $this->assertEquals(false, $oSess->UNITisSessionRequiredAction());
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $this->assertEquals(true, $oSess->UNITisSessionRequiredAction());
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
        $this->assertEquals(8, strlen($sTestToken));
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
        $this->assertEquals(8, strlen($sTestToken));
    }

    public function testGetRemoteAccessTokenTwice()
    {
        $oSubj = oxNew('oxSession');
        $oSubj->deleteVariable('_rtoken');
        $sToken1 = $oSubj->getRemoteAccessToken();
        $sToken2 = $oSubj->getRemoteAccessToken();

        $this->assertEquals($sToken1, $sToken2);
        $this->assertEquals(8, strlen($sToken2));
    }

    public function testIsRemoteAccessTokenValid()
    {
        $this->setRequestParameter('rtoken', 'test1');

        $oSubj = $this->getProxyClass('oxSession');
        $oSubj->setVariable('_rtoken', 'test1');
        $this->assertTrue($oSubj->UNITisValidRemoteAccessToken());
    }

    /**
     * Test handling of supplying an array instead of a string for rtoken.
     */
    public function DISABLED_testIsRemoteAccessTokenValidArrayRequestParameter()
    {
        //Suppress all error reporting on purpose for this test
        $originalErrorReportingLevel =  error_reporting(0);
        try {
            $this->setRequestParam('rtoken', array(1));

            $session = $this->getProxyClass('oxSession');
            $session->setVariable('_rtoken', 'test1');
            $this->assertFalse($session->_isValidRemoteAccessToken());
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
        $this->assertFalse($oSubj->UNITisValidRemoteAccessToken());
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

    public function testIsSessionStarted()
    {
        $oSession = $this->getProxyClass("oxSession");
        $this->assertFalse($oSession->isSessionStarted());

        // thats only way to test, cant wrap native "session_start()" function
        $oSession->setNonPublicVar("_blStarted", true);
        $this->assertTrue($oSession->isSessionStarted());
    }

    public function testIsActualSidInCookiePossitive()
    {
        $sOriginalVal = $_COOKIE["sid"];
        $_COOKIE["sid"] = "testIdDifferent";
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getId'));
        $oSession->expects($this->any())->method('getId')->will($this->returnValue('testId'));

        $blRes = $oSession->isActualSidInCookie();

        $_COOKIE["sid"] = $sOriginalVal;
        $this->assertFalse($blRes);
    }

    public function testIsActualSidInCookieNegative()
    {
        $sOriginalVal = $_COOKIE["sid"];
        $_COOKIE["sid"] = "testId";
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getId'));
        $oSession->expects($this->any())->method('getId')->will($this->returnValue('testId'));

        $blRes = $oSession->isActualSidInCookie();

        $_COOKIE["sid"] = $sOriginalVal;

        $this->assertTrue($blRes);
    }

    public function testIsActualSidInCookieNotSet()
    {
        $sOriginalVal = $_COOKIE["sid"];
        unset($_COOKIE["sid"]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getId'));
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
}
