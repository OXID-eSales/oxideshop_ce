<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\ShopVersion;
use oxSystemComponentException;
use oxUtilsHelper;
use \oxView;
use \oxField;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxUtilsHelper.php';

class modOxView extends oxView
{
    public function setVar($sName, $sValue)
    {
        $this->$sName = $sValue;
    }

    public static function reset()
    {
        self::$_blExecuted = false;
    }
}

class ViewTestFirstModuleController extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    public function doSomething()
    {
        return 'viewtestsecondmodulecontroller?fnc=doSomethingElse&someParameter=1';
    }

    protected function onExecuteNewAction()
    {
        throw new \Exception('Bail out before redirect, all is well.');
    }
}

class ViewTestSecondModuleController extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    public function doSomethingElse()
    {
    }
}



class ViewTest extends \OxidTestCase
{
    protected $_oView = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oView = oxNew('oxView');

        // backuping
        $this->_iSeoMode = $this->getConfig()->getActiveShop()->oxshops__oxseoactive->value;
        $this->getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField(0, oxField::T_RAW);

        oxRegistry::getUtils()->seoIsActive(true);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        modOxView::reset();

        // restoring
        $this->getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField($this->_iSeoMode, oxField::T_RAW);

        oxRegistry::getUtils()->seoIsActive(true);

        oxTestModules::cleanUp();

        parent::tearDown();
    }

    public function testIsDemoShop()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertFalse($oView->isDemoShop());
    }

    /*
     * Testing init
     */
    public function testInit()
    {
        $oView = oxNew('oxView');
        $oView->init();
        $this->assertEquals(1, preg_match("@\\BaseController@si", $oView->getThisAction()));

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, array('setOxCookie'));
        $oUtilsServer->expects($this->never())->method('setOxCookie');

        $this->addClassExtension(get_class($oUtilsServer), 'oxUtilsServer');
    }

    /*
     * Testing init
     */
    public function testInitForSeach()
    {
        $oView = oxNew('search');
        $oView->init();
        $this->assertEquals(strtolower(get_class($oView)), $oView->getThisAction());
    }

    /*
     * Test rendering components
     */
    public function testRender()
    {
        $oView = oxNew('oxView');
        $this->assertEquals('', $oView->render());
    }

    /**
     * Test if oxView::getTemplateName() is called from oxView::render()
     */
    public function testRenderMock()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getTemplateName"));
        $oView->expects($this->once())->method("getTemplateName")->will($this->returnValue("testTemplate.tpl"));
        $sRes = $oView->render();
        $this->assertEquals("testTemplate.tpl", $sRes);
    }

    /*
     * Test adding global params to view data
     */
    public function testAddGlobalParams()
    {
        $oView = oxNew('oxView');

        $oView->addGlobalParams();

        $aViewData = $oView->getViewData();

        $this->assertEquals($aViewData['oView'], $oView);
        $this->assertEquals($aViewData['oViewConf'], $oView->getViewConfig());
    }

    /*
     * Test adding global params to view data
     */
    public function testIsMall()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $oView = oxNew('oxView');
        $this->assertFalse($oView->isMall());
    }

    /*
     * Test adding additional data to _viewData
     */
    public function testSetAdditionalParams()
    {
        oxTestModules::addFunction('oxlang', 'getBaseLanguage', '{ return 1; }');
        $sParams = '';
        if (($sLang = oxRegistry::getLang()->getUrlLang())) {
            $sParams .= $sLang . "&amp;";
        }

        $oView = oxNew('oxView');
        $this->assertEquals($sParams, $oView->getAdditionalParams());
    }

    /*
     * Test adding data to _viewData
     */
    public function testAddTplParam()
    {
        $oView = oxNew('oxView');
        $oView->addTplParam('testName', 'testValue');
        $oView->addGlobalParams();

        $this->assertEquals('testValue', $oView->getViewDataElement('testName'));
    }

    /*
     * Test getTemplateName()
     */
    public function testSetGetTemplateName()
    {
        $oView = oxNew('oxView');
        $oView->setTemplateName("testTemplate");

        $this->assertEquals('testTemplate', $oView->getTemplateName());
    }

    /*
     * Test set/get class name
     */
    public function testSetGetClassName()
    {
        $this->_oView->setClassName('123456789');
        $this->assertEquals('123456789', $this->_oView->getClassName());
    }

    /*
     * Test set/get function name
     */
    public function testSetGetFncName()
    {
        $this->_oView->setFncName('123456789');
        $this->assertEquals('123456789', $this->_oView->getFncName());
    }

    /*
     * Test set/get view data
     */
    public function testSetViewData()
    {
        $this->_oView->setViewData(array('1a', '2b'));
        $this->assertEquals(array('1a', '2b'), $this->_oView->getViewData());
    }

    /*
     * Test get view data component
     */
    public function testGetViewDataElement()
    {
        $this->_oView->setViewData(array('aa' => 'aaValue', 'bb' => 'bbValue'));
        $this->assertEquals('aaValue', $this->_oView->getViewDataElement('aa'));
    }

    /*
     * Test set/get class location
     */
    public function testClassLocation()
    {
        $this->_oView->setClassLocation('123456789');
        $this->assertEquals('123456789', $this->_oView->getClassLocation());
    }

    /*
     * Test set/get view action
     */
    public function testThisAction()
    {
        $this->_oView->setThisAction('123456789');
        $this->assertEquals('123456789', $this->_oView->getThisAction());
    }

    /*
     * Test set/get parent
     */
    public function testParent()
    {
        $this->_oView->setParent('123456789');
        $this->assertEquals('123456789', $this->_oView->getParent());
    }

    /*
     * Test set/get is component
     */
    public function testIsComponent()
    {
        $this->_oView->setIsComponent('123456789');
        $this->assertEquals('123456789', $this->_oView->getIsComponent());
    }

    /**
     * Testing function execution code
     */
    public function testExecuteFunction()
    {
        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\modOxView::class, array('xxx', '_executeNewAction'));
        $oView->expects($this->once())->method('xxx')->will($this->returnValue('xxx'));
        $oView->expects($this->once())->method('_executeNewAction')->with($this->equalTo('xxx'));
        $oView->executeFunction('xxx');
    }

    public function testExecuteFunctionExecutesComponentFunction()
    {
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, array('xxx'));
        $oCmp->expects($this->never())->method('xxx');
        $this->assertNull($oCmp->executeFunction('yyy'));
    }

    public function testExecuteFunctionThrowsExeption()
    {
        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\modOxView::class, array('xxx'));
        $oView->expects($this->never())->method('xxx');


        try {
            $oView->executeFunction('yyy');
        } catch (oxSystemComponentException $oEx) {
            $this->assertEquals("ERROR_MESSAGE_SYSTEMCOMPONENT_FUNCTIONNOTFOUND yyy", $oEx->getMessage());

            return;
        }

        $this->fail("No exception thrown by executeFunction");
    }

    public function testExecuteFunctionExecutesOnlyOnce()
    {
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, array('xxx'));
        $oCmp->expects($this->once())->method('xxx');

        $oCmp->executeFunction('xxx');
        $oCmp->executeFunction('xxx');
    }

    /**
     * New action url getter tests, case we try to redirect to a not existing class
     */
    public function testExecuteNewActionNotExistingClass()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsHelper", "oxutils");

        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $config->expects($this->never())->method('isSsl');
        $config->expects($this->never())->method('getSslShopUrl');
        $config->expects($this->never())->method('getShopUrl');

        $view = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'));
        $view->expects($this->once())->method('getConfig')->will($this->returnValue($config));

        $this->expectException('oxSystemComponentException');
        $this->expectExceptionMessage('ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND' . ' testAction');
        $view->_executeNewAction("testAction");
    }

    /**
     * New action url getter tests
     */
    public function testExecuteNewActionNonSsl()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsHelper", "oxutils");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('shopurl/'));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->_executeNewAction("details");
        $this->assertEquals('shopurl/index.php?cl=details&' . $this->getSession()->sid(), oxUtilsHelper::$sRedirectUrl);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('shopurl/'));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->_executeNewAction("details?someparam=12");
        $this->assertEquals("shopurl/index.php?cl=details&someparam=12&" . $this->getSession()->sid(), oxUtilsHelper::$sRedirectUrl);
    }

    public function testExecuteNewActionSsl()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsHelper", "oxutils");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->once())->method('getSslShopUrl')->will($this->returnValue('SSLshopurl/'));
        $oConfig->expects($this->never())->method('getShopUrl');

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->UNITexecuteNewAction("details?fnc=somefnc&anid=someanid");
        $this->assertEquals('SSLshopurl/index.php?cl=details&fnc=somefnc&anid=someanid&' . $this->getSession()->sid(), oxUtilsHelper::$sRedirectUrl);
    }

    public function testExecuteNewActionSslIsAdmin()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsHelper", "oxutils");

        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('isSsl', 'getSslShopUrl', 'getShopUrl'));
        $config->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $config->expects($this->once())->method('getSslShopUrl')->will($this->returnValue('SSLshopurl/'));
        $config->expects($this->never())->method('getShopUrl');
        $config->setConfigParam('sAdminDir', 'admin');

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig', 'isAdmin'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($config));
        $oView->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oView->UNITexecuteNewAction("details?fnc=somefnc&anid=someanid");
        $this->assertEquals('SSLshopurl/admin/index.php?cl=details&fnc=somefnc&anid=someanid&' . $this->getSession()->sid(), oxUtilsHelper::$sRedirectUrl);
    }

    public function testGetCharSet()
    {
        $oView = oxNew('oxView');
        $this->assertEquals('UTF-8', $oView->getCharSet());
    }

    public function testGetShopVersion()
    {
        $oView = oxNew('oxView');
        $this->assertEquals(ShopVersion::getVersion(), $oView->getShopVersion());
    }

    public function testIsDemoVersion()
    {
        $oView = oxNew('oxView');
        if ($this->getConfig()->detectVersion() == 1) {
            $this->assertTrue($oView->isDemoVersion());
        } else {
            $this->assertFalse($oView->isDemoVersion());
        }
    }

    /**
     * testIsBetaVersion data provider.
     */
    public function _dptestIsBetaVersion()
    {
        return array(
            array('5.1.0', false),
            array('5.1.0_beta', true),
            array('5.1.0_beta1', true),
            array('5.1.0_rc', false),
            array('5.1.0_rc1', false),
        );
    }

    /**
     * @dataProvider _dptestIsBetaVersion
     */
    public function testIsBetaVersion($getVersion, $isBetaVersion)
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getVersion'));
        $oConfig->expects($this->any())->method('getVersion')->will($this->returnValue($getVersion));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals($isBetaVersion, $oView->isBetaVersion());
    }

    /**
     * testIsRCVersion data provider.
     */
    public function _dptestIsRCVersion()
    {
        return array(
            array('5.1.0', false),
            array('5.1.0_beta', false),
            array('5.1.0_beta1', false),
            array('5.1.0_rc', true),
            array('5.1.0_rc1', true),
        );
    }

    /**
     * @dataProvider _dptestIsRCVersion
     */
    public function testIsRCVersion($getVersion, $isRCVersion)
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getVersion'));
        $oConfig->expects($this->any())->method('getVersion')->will($this->returnValue($getVersion));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getConfig'), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals($isRCVersion, $oView->isRCVersion());
    }

    /**
     * testShowBetaBote data provider.
     */
    public function _dptestShowBetaNote()
    {
        return array(
            array(false, false, false),
            array(true, false, true),
            array(false, true, true),
            array(true, true, true),
        );
    }

    /**
     * @dataProvider _dptestShowBetaNote
     */
    public function testShowBetaNote($isBetaVersion, $isRCVersion, $showBetaNote)
    {
        $oxView = oxNew('oxView');
        if (!$oxView->showBetaNote()) {
            $this->markTestSkipped('there is no real beta note for this version');
        }

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('isBetaVersion', 'isRCVersion'), array(), '', false);
        $oView->expects($this->any())->method('isBetaVersion')->will($this->returnValue($isBetaVersion));
        $oView->expects($this->any())->method('isRCVersion')->will($this->returnValue($isRCVersion));

        $this->assertEquals($showBetaNote, $oView->showBetaNote());
    }

    public function testEditionIsNotEmpty()
    {
        //edition is always set
        $oView = oxNew('oxView');
        $sEdition = $oView->getShopEdition();
        $this->assertNotSame('', $sEdition);
    }

    public function testGetEdition()
    {
        //edition is always set
        $oView = oxNew('oxView');
        $viewEdition = $oView->getShopEdition();

        $expectedEdition = $this->getTestConfig()->getShopEdition();
        $this->assertEquals($expectedEdition, $viewEdition);
    }


    public function testGetFullEdition()
    {
        $expected = 'Community Edition';
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $expected = 'Enterprise Edition';
        } elseif ($this->getTestConfig()->getShopEdition() === 'PE') {
            $expected = 'Professional Edition';
        }

        //edition is always set
        $oView = oxNew('oxView');
        $sEdition = $oView->getShopFullEdition();
        $this->assertEquals($expected, $sEdition);
    }

    /**
     * Testing special getters setters
     */
    public function testGetCategoryIdAndSetCategoryId()
    {
        $oView = oxNew('oxView');
        $this->assertNull($oView->getCategoryId());

        $this->setRequestParameter('cnid', 'xxx');
        $this->assertEquals('xxx', $oView->getCategoryId());

        // additionally checking cache
        $this->setRequestParameter('cnid', null);
        $this->assertEquals('xxx', $oView->getCategoryId());

        $oView->setCategoryId('yyy');
        $this->assertEquals('yyy', $oView->getCategoryId());
    }

    public function testGetActionClassName()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('getClassName'));
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue('className'));

        $this->assertEquals('className', $oView->getActionClassName());
    }

    public function testIsCallForCache()
    {
        $oView = oxNew('oxView');
        $oView->setIsCallForCache('123456789');
        $this->assertEquals('123456789', $oView->getIsCallForCache());
    }

    /*
     * Testing oxview::getViewId()
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = oxNew('oxView');
        $this->assertNull($oView->getViewId());
    }

    public function testShowRdfa()
    {
        $oView = oxNew('oxview');
        $this->assertFalse($oView->showRdfa());
    }

    public function testSetGetViewParameters()
    {
        $oView = oxNew('oxview');

        $oView->setViewParameters(array("testItem1" => "testValue1", "testItem2" => "testValue2"));

        $this->assertEquals("testValue1", $oView->getViewParameter("testItem1"));
        $this->assertEquals("testValue2", $oView->getViewParameter("testItem2"));
        $this->assertNull($oView->getViewParameter("testItem3"));
    }

    public function testShowNewsletter()
    {
        $oView = oxNew('oxView');
        $this->assertEquals(1, $oView->showNewsletter());
    }

    public function testSetShowNewsletter()
    {
        $oView = oxNew('oxView');
        $oView->setShowNewsletter(0);

        $this->assertEquals(0, $oView->showNewsletter());
    }

    /**
     * oxView::getBelboonParam() test case
     *
     * @return null
     */

    public function testGetBelboonParam()
    {
        $sTest = "testValue";
        $this->getSession()->setVariable('belboon', $sTest);

        $oView = oxNew('oxview');
        $this->assertEquals($sTest, $oView->getBelboonParam());

        //other test case
        $this->getSession()->setVariable('belboon', false);
        $this->assertEquals('', $oView->getBelboonParam());

        //other test case
        $sTest2 = "testValue2";

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("setVariable"));
        $oSession->expects($this->once())->method("setVariable")->with($this->equalTo('belboon'));

        $this->getSession()->setVariable('belboon', false);
        $this->setRequestParameter('belboon', $sTest2);
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getSession"));
        $oView->expects($this->exactly(2))->method("getSession")->will($this->returnValue($oSession));
        $this->assertEquals($sTest2, $oView->getBelboonParam());
    }

    /**
     * oxView::getRevision() test case
     *
     * @return null
     */

    public function testGetRevision()
    {
        $sTest = "testRevision";
        $this->getConfig()->setConfigParam("blStockOnDefaultMessage", $sTest);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getRevision"));
        $oConfig->expects($this->once())->method("getRevision")->will($this->returnValue($sTest));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getConfig"));
        $oView->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals($sTest, $oView->getRevision());
    }

    public function testGetSidForWidget()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isActualSidInCookie', 'getId'));
        $oSession->expects($this->once())->method('isActualSidInCookie')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue('testSid'));

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getSession"));
        $oView->expects($this->any())->method("getSession")->will($this->returnValue($oSession));

        $this->assertEquals('testSid', $oView->getSidForWidget());
    }

    public function testGetSidForWidget_CookieInSessionMatchesActualSid_expectNull()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('isActualSidInCookie', 'getId'));
        $oSession->expects($this->once())->method('isActualSidInCookie')->will($this->returnValue(true));
        $oSession->expects($this->never())->method('getId');

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getSession"));
        $oView->expects($this->any())->method("getSession")->will($this->returnValue($oSession));

        $this->assertNull($oView->getSidForWidget());
    }

    /**
     * Verify that also module metadata v2 controller ids are handled correctly.
     * Test case that controller id does not match any class.
     */
    public function testExecuteFunctionForUnmatchedModuleController()
    {
        $toBeExecuted = 'viewtestmodulecontroller?fnc=doSomethingElse&someParameter=1';

        $view = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\modOxView::class, array('doSomething'));
        $view->expects($this->once())->method('doSomething')->will($this->returnValue($toBeExecuted));

        try {
            $view->executeFunction('doSomething');
        } catch (\OxidEsales\Eshop\Core\Exception\SystemComponentException $exception) {
            $this->assertEquals('ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND viewtestmodulecontroller', $exception->getMessage());
            return;
        }

        $this->fail('No exception thrown by executeFunction');
    }

    /**
     * Verify that also module metadata v2 controller ids are handled correctly.
     * Test case that controller id does match a module controller class.
     */
    public function testExecuteFunctionForModuleController()
    {
        \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::resetModuleVariables();
        $controllers = ['viewtestmodule' =>
                            ['viewtestsecondmodulecontroller' => \OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\ViewTestSecondModuleController::class]
                       ];
        $storageKey = \OxidEsales\Eshop\Core\Routing\Module\ClassProviderStorage::STORAGE_KEY;
        $this->getModuleVariableLocator()->setModuleVariable($storageKey, $controllers);

        $this->assertEmpty(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('ViewTestModuleControllerResult'));
        $view = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\ViewTestFirstModuleController::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bail out before redirect, all is well.');
        $view->executeFunction('doSomething');
    }

    /**
     * Test helper, easiest way be able to use ModuleVariableLocator::setModuleVariable() without using the
     * deprecated method UtilsObject::setModuleVar().
     *
     * @return object \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator
     */
    private function getModuleVariableLocator()
    {
        $cache = $this->getMock(\OxidEsales\Eshop\Core\FileCache::class);
        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue($this->getShopId()));

        return oxNew(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, $cache, $shopIdCalculator);
    }
}
