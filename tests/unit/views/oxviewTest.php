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

class modOxView extends oxView
{

    public function setVar($sName, $sValue)
    {
        $this->$sName = $sValue;
    }

    static public function reset()
    {
        self::$_blExecuted = false;
    }
}

class oxUtilsRedirectForoxviewTest extends oxUtils
{

    public $sRedirectUrl = null;

    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301)
    {
        $this->sRedirectUrl = $sUrl;
    }
}

class Unit_Views_oxviewTest extends OxidTestCase
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
        $this->_oView = new oxView;

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
        $oConfig = $this->getMock('oxconfig', array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        $oView = $this->getMock('oxview', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertFalse($oView->isDemoShop());
    }

    /*
     * Testing init
     */
    public function testInit()
    {
        $oView = new oxView();
        $oView->init();
        $this->assertEquals("oxview", $oView->getThisAction());

        $oUtilsServer = $this->getMock('oxUtilsServer', array('setOxCookie'));
        $oUtilsServer->expects($this->never())->method('setOxCookie');

        modInstances::addMod("oxUtilsServer", $oUtilsServer);
    }

    /*
     * Testing init
     */
    public function testInitForSeach()
    {
        $oView = new search();
        $oView->init();
        $this->assertEquals("search", $oView->getThisAction());
    }

    /*
     * Test rendering components
     */
    public function testRender()
    {
        $oView = new oxView();
        $this->assertEquals('', $oView->render());
    }

    /**
     * Test if oxView::getTemplateName() is called from oxView::render()
     */
    public function testRenderMock()
    {
        $oView = $this->getMock("oxview", array("getTemplateName"));
        $oView->expects($this->once())->method("getTemplateName")->will($this->returnValue("testTemplate.tpl"));
        $sRes = $oView->render();
        $this->assertEquals("testTemplate.tpl", $sRes);
    }

    /*
     * Test adding global params to view data
     */
    public function testAddGlobalParams()
    {
        $myConfig = $this->getConfig();

        $oView = new oxView();

        $oView->addGlobalParams();

        $aViewData = $oView->getViewData();


        $this->assertEquals($aViewData['oView'], $oView);
        $this->assertEquals($aViewData['oViewConf'], $oView->getViewConfig());
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

        $oView = new oxView();
        $this->assertEquals($sParams, $oView->getAdditionalParams());
    }

    /*
     * Test adding data to _viewData
     */
    public function testAddTplParam()
    {
        $oView = new oxView();
        $oView->addTplParam('testName', 'testValue');
        $oView->addGlobalParams();

        $this->assertEquals('testValue', $oView->getViewDataElement('testName'));
    }

    /*
     * Test getTemplateName()
     */
    public function testSetGetTemplateName()
    {
        $oView = new oxView();
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
        $oView = $this->getMock('modOxView', array('xxx', '_executeNewAction'));
        $oView->expects($this->once())->method('xxx')->will($this->returnValue('xxx'));
        $oView->expects($this->once())->method('_executeNewAction')->with($this->equalTo('xxx'));
        $oView->executeFunction('xxx');
    }

    public function testExecuteFunctionExecutesComponentFunction()
    {
        $oCmp = $this->getMock('oxcmp_categories', array('xxx'));
        $oCmp->expects($this->never())->method('xxx');
        $this->assertNull($oCmp->executeFunction('yyy'));
    }

    public function testExecuteFunctionThrowsExeption()
    {
        $oView = $this->getMock('modOxView', array('xxx'));
        $oView->expects($this->never())->method('xxx');


        try {
            $oView->executeFunction('yyy');
        } catch (oxSystemComponentException $oEx) {
            $this->assertEquals("ERROR_MESSAGE_SYSTEMCOMPONENT_FUNCTIONNOTFOUND", $oEx->getMessage());

            return;
        }

        $this->fail("No exception thrown by executeFunction");
    }

    public function testExecuteFunctionExecutesOnlyOnce()
    {
        $oCmp = $this->getMock('oxcmp_categories', array('xxx'));
        $oCmp->expects($this->once())->method('xxx');

        $oCmp->executeFunction('xxx');
        $oCmp->executeFunction('xxx');
    }

    /**
     * oxView::executeFunction() test case
     *
     * @return null
     */


    /**
     * New action url getter tests, case we try to redirect to a not existing class
     */
    public function testExecuteNewActionNotExistingClass()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsRedirectForoxviewTest", "oxutils");

        $oConfig = $this->getMock('oxconfig', array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->never())->method('isSsl');
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->never())->method('getShopUrl');

        $oView = $this->getMock('oxview', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->setExpectedException('oxSystemComponentException', 'ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND');
        $oView->UNITexecuteNewAction("testAction");
    }

    /**
     * New action url getter tests
     */
    public function testExecuteNewActionNonSsl()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsRedirectForoxviewTest", "oxutils");

        $oConfig = $this->getMock('oxconfig', array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('shopurl/'));

        $oView = $this->getMock('oxview', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->UNITexecuteNewAction("details");
        $this->assertEquals('shopurl/index.php?cl=details&' . $this->getSession()->sid(), oxRegistry::getUtils()->sRedirectUrl);

        $oConfig = $this->getMock('oxconfig', array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getSslShopUrl');
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('shopurl/'));

        $oView = $this->getMock('oxview', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->UNITexecuteNewAction("details?someparam=12");
        $this->assertEquals("shopurl/index.php?cl=details&someparam=12&" . $this->getSession()->sid(), oxRegistry::getUtils()->sRedirectUrl);

    }

    public function testExecuteNewActionSsl()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsRedirectForoxviewTest", "oxutils");

        $oConfig = $this->getMock('oxconfig', array('getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->will($this->returnValue('oxid.php'));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->once())->method('getSslShopUrl')->will($this->returnValue('SSLshopurl/'));
        $oConfig->expects($this->never())->method('getShopUrl');

        $oView = $this->getMock('oxview', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $sUrl = $oView->UNITexecuteNewAction("details?fnc=somefnc&anid=someanid");
        $this->assertEquals('SSLshopurl/index.php?cl=details&fnc=somefnc&anid=someanid&' . $this->getSession()->sid(), oxRegistry::getUtils()->sRedirectUrl);
    }

    public function testExecuteNewActionSslIsAdmin()
    {
        $this->getSession()->setId('SID');

        oxAddClassModule("oxUtilsRedirectForoxviewTest", "oxutils");

        $config = $this->getMock('oxconfig', array('isSsl', 'getSslShopUrl', 'getShopUrl'));
        $config->expects($this->once())->method('isSsl')->will($this->returnValue(true));
        $config->expects($this->once())->method('getSslShopUrl')->will($this->returnValue('SSLshopurl/'));
        $config->expects($this->never())->method('getShopUrl');
        $config->setConfigParam('sAdminDir', 'admin');

        $oView = $this->getMock('oxview', array('getConfig', 'isAdmin'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($config));
        $oView->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $sUrl = $oView->UNITexecuteNewAction("details?fnc=somefnc&anid=someanid");
        $this->assertEquals('SSLshopurl/admin/index.php?cl=details&fnc=somefnc&anid=someanid&' . $this->getSession()->sid(), oxRegistry::getUtils()->sRedirectUrl);
    }

    /**
     * oxView::_executeNewAction() test case
     *
     * @return null
     */

    public function testGetTrustedShopIdNotValid()
    {
        $oView = new oxView();
        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', array(0 => 'aaa'));

        $this->assertFalse($oView->getTrustedShopId());
    }

    public function testGetTrustedShopIdIfNotMultilanguage()
    {
        $oView = new oxView();
        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('tsSealType', array(0 => 'CLASSIC'));
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', 'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
        $this->assertEquals('XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', $oView->getTrustedShopId());
    }

    public function testGetTrustedShopIdIfNotMultilanguageNotValid()
    {
        $oView = new oxView();
        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', 'XXX');
        $this->assertFalse($oView->getTrustedShopId());
    }

    public function testGetTrustedShopId()
    {
        $oView = $this->getProxyClass('oxview');
        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('tsSealType', array(0 => 'CLASSIC'));
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', array(0 => 'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'));

        $this->assertEquals('XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', $oView->getTrustedShopId());
    }

    public function testGetTrustedShopIdNotActive()
    {
        $oView = new oxView();
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', null);

        $this->assertFalse($oView->getTrustedShopId());
    }

    /**
     * oxView::getTrustedShopId() test case
     *
     * @return null
     */

    public function testGetTrustedShopIdFalse()
    {
        $oView = new oxView();
        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('tsSealType', array(0 => 'CLASSIC'));
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', 'ABC');
        $this->assertFalse($oView->getTrustedShopId());
    }

    /**
     * oxView::getTSExcellenceId() test case
     *
     * @return null
     */

    public function testGetTSExcellenceId()
    {
        $sTest = "testValue";

        $oView = $this->getProxyClass('oxView');
        $oView->setNonPublicVar('_sTSExcellenceId', $sTest);
        $this->assertEquals($sTest, $oView->getTSExcellenceId());
    }

    /**
     * oxView::getTSExcellenceId() test case
     *
     * @return null
     */

    public function testGetTSExcellenceIdNull()
    {
        $sTest = "testValue";
        $iTest = 0;

        $oView = $this->getProxyClass('oxView');
        $oView->setNonPublicVar('_sTSExcellenceId', null);

        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('tsSealType', array(0 => 'EXCELLENCE'));
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', $sTest);

        $oLang = $this->getMock("oxLang", array("getBaseLanguage"));
        $oLang->expects($this->any())->method("getBaseLanguage")->will($this->returnValue($iTest));
        oxregistry::set('oxLang', $oLang);

        $this->assertEquals($sTest[$iTest], $oView->getTSExcellenceId());
    }

    /**
     * oxView::getTSExcellenceId() test case
     *
     * @return null
     */

    public function testGetTSExcellenceIdNullWrongSealType()
    {
        $sTest = "testValue";
        $iTest = 0;

        $oView = $this->getProxyClass('oxView');
        $oView->setNonPublicVar('_sTSExcellenceId', null);

        $this->getConfig()->setConfigParam('tsSealActive', 1);
        $this->getConfig()->setConfigParam('tsSealType', array(0 => 'WRONG_TYPE'));
        $this->getConfig()->setConfigParam('iShopID_TrustedShops', $sTest);

        $oLang = $this->getMock("oxLang", array("getBaseLanguage"));
        $oLang->expects($this->any())->method("getBaseLanguage")->will($this->returnValue($iTest));
        oxregistry::set('oxLang', $oLang);

        $this->assertEquals('', $oView->getTSExcellenceId());
    }

    public function testGetCharSet()
    {
        $oView = new oxView();
        $this->assertEquals('ISO-8859-15', $oView->getCharSet());
    }

    public function testGetShopVersion()
    {
        $oView = new oxView();
        $this->assertEquals($this->getConfig()->getActiveShop()->oxshops__oxversion->value, $oView->getShopVersion());
    }

    public function testIsDemoVersion()
    {
        $oView = new oxView();
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
        $oConfig = $this->getMock('oxConfig', array('getVersion'));
        $oConfig->expects($this->any())->method('getVersion')->will($this->returnValue($getVersion));

        $oView = $this->getMock("oxView", array('getConfig'), array(), '', false);
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
        $oConfig = $this->getMock('oxConfig', array('getVersion'));
        $oConfig->expects($this->any())->method('getVersion')->will($this->returnValue($getVersion));

        $oView = $this->getMock("oxView", array('getConfig'), array(), '', false);
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
        $oxView = new oxView();
        if (!$oxView->showBetaNote()) {
            $this->markTestSkipped('there is no real beta note for this version');
        }

        $oView = $this->getMock("oxView", array('isBetaVersion', 'isRCVersion'), array(), '', false);
        $oView->expects($this->any())->method('isBetaVersion')->will($this->returnValue($isBetaVersion));
        $oView->expects($this->any())->method('isRCVersion')->will($this->returnValue($isRCVersion));

        $this->assertEquals($showBetaNote, $oView->showBetaNote());
    }

    public function testEditionIsNotEmpty()
    {
        //edition is always set
        $oView = new oxView();
        $sEdition = $oView->getShopEdition();
        $this->assertNotSame('', $sEdition);
    }

    public function testGetEdition()
    {
        //edition is always set
        $oView = new oxView();
        $sEdition = $oView->getShopEdition();

            $this->assertTrue($sEdition == "CE" || $sEdition == "PE");

    }


    public function testFullEditionIsNotEmpty()
    {
        //edition is always set
        $oView = new oxView();
        $sEdition = $oView->getShopFullEdition();
        $this->assertNotSame('', $sEdition);
    }

    public function testGetFullEdition()
    {
        //edition is always set
        $oView = new oxView();
        $sEdition = $oView->getShopFullEdition();

            $this->assertEquals("Community Edition", $sEdition);

    }

    /**
     * Testing special getters setters
     */
    public function testGetCategoryIdAndSetCategoryId()
    {
        $oView = new oxView();
        $this->assertNull($oView->getCategoryId());

        $this->getConfig()->setRequestParameter('cnid', 'xxx');
        $this->assertEquals('xxx', $oView->getCategoryId());

        // additionally checking cache
        $this->getConfig()->setRequestParameter('cnid', null);
        $this->assertEquals('xxx', $oView->getCategoryId());

        $oView->setCategoryId('yyy');
        $this->assertEquals('yyy', $oView->getCategoryId());
    }

    public function testGetActionClassName()
    {
        $oView = $this->getMock('oxView', array('getClassName'));
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue('className'));

        $this->assertEquals('className', $oView->getActionClassName());
    }

    /**
     * Testing getter for checking if user is connected using Facebook connect
     *
     * return null
     */
    public function testIsConnectedWithFb()
    {
        $oFB = $this->getMock("oxFb", array("isConnected"));
        $oFB->expects($this->any())->method("isConnected")->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxFb', $oFB);
        $oView = new oxView();

        $this->setConfigParam("bl_showFbConnect", true);
        $this->assertTrue($oView->isConnectedWithFb());

        $this->setConfigParam("bl_showFbConnect", false);
        $this->assertFalse($oView->isConnectedWithFb());
    }

    /**
     * Testing getter for checking if user is connected using Facebook connect
     *
     * return null
     */
    public function testIsNotConnectedWithFb()
    {
        $oFB = $this->getMock("oxFb", array("isConnected"));
        $oFB->expects($this->any())->method("isConnected")->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxFb', $oFB);

        $this->setConfigParam("bl_showFbConnect", true);

        $oView = new oxView();
        $this->assertFalse($oView->isConnectedWithFb());
    }

    /**
     * Testing getting connected with Facebook connect user id
     *
     * return null
     */
    public function testGetFbUserId()
    {
        oxTestModules::addFunction("oxFb", "getUser", "{return 123;}");

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("bl_showFbConnect", false);

        $oView = new oxView();
        $this->assertNull($oView->getFbUserId());

        $myConfig->setConfigParam("bl_showFbConnect", true);
        $this->assertEquals("123", $oView->getFbUserId());
    }

    /**
     * Testing getting true or false for showing popup after user
     * connected using Facebook connect - FB connect is disabled
     *
     * return null
     */
    public function testShowFbConnectToAccountMsg_FbConnectIsOff()
    {
        $myConfig = $this->getConfig();
        $myConfig->setRequestParameter("fblogin", false);

        $oView = new oxView();
        $this->assertFalse($oView->showFbConnectToAccountMsg());
    }

    /**
     * Testing getting true or false for showing popup after user
     * connected using Facebook connect - FB connect is enabled
     * user connected using FB, but does not has account in shop
     *
     * return null
     */
    public function testShowFbConnectToAccountMsg_FbOn_NoAccount()
    {
        $myConfig = $this->getConfig();
        $myConfig->setRequestParameter("fblogin", true);

        $oView = $this->getMock('oxview', array('getUser'));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(null));

        $this->assertTrue($oView->showFbConnectToAccountMsg());
    }

    /**
     * Testing getting true or false for showing popup after user
     * connected using Facebook connect - FB connect is enabled
     * user connected using FB and has account in shop
     *
     * return null
     */
    public function testShowFbConnectToAccountMsg_FbOn_AccountOn()
    {
        $myConfig = $this->getConfig();
        $myConfig->setRequestParameter("fblogin", true);
        $oUser = new oxUser();

        $oView = $this->getMock('oxview', array('getUser'));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertFalse($oView->showFbConnectToAccountMsg());
    }

    /**
     * Testing mall mode getter
     */
    public function testIsMall()
    {
        $oView = new oxView();
            $this->assertFalse($oView->isMall());
    }

    public function testIsCallForCache()
    {
        $oView = new oxView();
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
        $oView = new oxView();
        $this->assertNull($oView->getViewId());
    }

    public function testShowRdfa()
    {
        $oView = new oxview();
        $this->assertFalse($oView->showRdfa());
    }

    public function testSetGetViewParameters()
    {
        $oView = new oxview();

        $oView->setViewParameters(array("testItem1" => "testValue1", "testItem2" => "testValue2"));

        $this->assertEquals("testValue1", $oView->getViewParameter("testItem1"));
        $this->assertEquals("testValue2", $oView->getViewParameter("testItem2"));
        $this->assertNull($oView->getViewParameter("testItem3"));
    }


    public function testShowNewsletter()
    {
        $oView = new oxView();
        $this->assertEquals(1, $oView->showNewsletter());
    }

    public function testSetShowNewsletter()
    {
        $oView = new oxView();
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

        $oView = new oxview();
        $this->assertEquals($sTest, $oView->getBelboonParam());

        //other test case
        $this->getSession()->setVariable('belboon', false);
        $this->assertEquals('', $oView->getBelboonParam());

        //other test case
        $sTest2 = "testValue2";

        $oSession = $this->getMock("oxSession", array("setVariable"));
        $oSession->expects($this->once())->method("setVariable")->with($this->equalTo('belboon'));

        $this->getSession()->setVariable('belboon', false);
        $this->setRequestParam('belboon', $sTest2);
        $oView = $this->getMock("oxView", array("getSession"));
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

        $oConfig = $this->getMock("oxConfig", array("getRevision"));
        $oConfig->expects($this->once())->method("getRevision")->will($this->returnValue($sTest));

        $oView = $this->getMock("oxView", array("getConfig"));
        $oView->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals($sTest, $oView->getRevision());
    }

    public function testGetSidForWidget()
    {
        $oSession = $this->getMock('oxSession', array('isActualSidInCookie', 'getId'));
        $oSession->expects($this->once())->method('isActualSidInCookie')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue('testSid'));

        $oView = $this->getMock("oxView", array("getSession"));
        $oView->expects($this->any())->method("getSession")->will($this->returnValue($oSession));

        $this->assertEquals('testSid', $oView->getSidForWidget());
    }

    public function testGetSidForWidget_CookieInSessionMatchesActualSid_expectNull()
    {
        $oSession = $this->getMock('oxSession', array('isActualSidInCookie', 'getId'));
        $oSession->expects($this->once())->method('isActualSidInCookie')->will($this->returnValue(true));
        $oSession->expects($this->never())->method('getId');

        $oView = $this->getMock("oxView", array("getSession"));
        $oView->expects($this->any())->method("getSession")->will($this->returnValue($oSession));

        $this->assertNull($oView->getSidForWidget());
    }


}
