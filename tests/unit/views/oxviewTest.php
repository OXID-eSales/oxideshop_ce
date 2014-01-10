<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class modOxView extends oxView
{
    public function setVar( $sName, $sValue )
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

    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301 )
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
        $this->_iSeoMode = oxConfig::getInstance()->getActiveShop()->oxshops__oxseoactive->value;
        oxConfig::getInstance()->getActiveShop()->oxshops__oxseoactive = new oxField(0, oxField::T_RAW);

        oxUtils::getInstance()->seoIsActive( true );
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
        oxConfig::getInstance()->getActiveShop()->oxshops__oxseoactive = new oxField($this->_iSeoMode, oxField::T_RAW);

        oxUtils::getInstance()->seoIsActive( true );

        oxTestModules::addFunction("oxseoencoder", "unsetInstance", "{oxSeoEncoder::\$_instance = null;}");
        $oE = oxNew('oxseoencoder');
        $oE->unsetInstance();
        oxTestModules::cleanUp();

        parent::tearDown();
    }

    public function testIsDemoShop()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'isDemoShop' ) );
        $oConfig->expects( $this->once() )->method( 'isDemoShop')->will( $this->returnValue( false ) );

        $oView = $this->getMock( 'oxview', array( 'getConfig' ) );
        $oView->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertFalse( $oView->isDemoShop() );
    }

    /*
     * Testing init
     */
    public function testInit()
    {
        $oView = oxNew( 'oxView' );
        $oView->init();
        $this->assertEquals( "oxview", $oView->getThisAction() );
    }

    /*
     * Testing init
     */
    public function testInitForSeach()
    {
        $oView = oxNew( 'search' );
        $oView->init();
        $this->assertEquals( "search", $oView->getThisAction() );
    }

    /*
     * Test rendering components
     */
    public function testRender()
    {
        $oView = new oxView();
        $this->assertEquals( '', $oView->render() );
    }

    /**
     * Test if oxView::getTemplateName() is called from oxView::render()
     */
    public function testRenderMock()
    {
        $oView = $this->getMock("oxview", array("getTemplateName"));
        $oView->expects( $this->once() )->method("getTemplateName")->will( $this->returnValue("testTemplate.tpl") );
        $sRes = $oView->render();
        $this->assertEquals("testTemplate.tpl", $sRes);
    }

    /*
     * Test adding global params to view data
     */
    public function testAddGlobalParams()
    {
        $myConfig = oxConfig::getInstance();

        $oView = oxNew( 'oxView' );

        $oView->addGlobalParams();

        $aViewData = $oView->getViewData();


        $this->assertEquals( $aViewData['oView'], $oView );
        $this->assertEquals( $aViewData['oViewConf'], $oView->getViewConfig() );
    }

    /*
     * Test adding additional data to _viewData
     */
    public function testSetAdditionalParams()
    {
        oxTestModules::addFunction('oxlang', 'getBaseLanguage', '{ return 1; }');
        $sParams = '';
        if ( ( $sLang = oxLang::getInstance()->getUrlLang() ) ) {
            $sParams .= $sLang."&amp;";
        }

        $oView = oxNew( 'oxView' );
        $this->assertEquals( $sParams, $oView->getAdditionalParams() );
    }

    /*
     * Test adding data to _viewData
     */
    public function testAddTplParam()
    {
        $oView = oxNew( 'oxView' );
        $oView->addTplParam( 'testName', 'testValue' );
        $oView->addGlobalParams();

        $this->assertEquals( 'testValue', $oView->getViewDataElement('testName') );
    }

    /*
     * Test getTemplateName()
     */
    public function testGetTemplateName()
    {
        $oView = $this->getProxyClass( "oxView" );
        $oView->setNonPublicVar( "_sThisTemplate", "testTemplate" );

        $this->assertEquals( 'testTemplate', $oView->getTemplateName() );
    }

    /*
     * Test set/get class name
     */
    public function testSetGetClassName()
    {
        $this->_oView->setClassName( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getClassName() );
    }

    /*
     * Test set/get function name
     */
    public function testSetGetFncName()
    {
        $this->_oView->setFncName( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getFncName() );
    }

    /*
     * Test set/get view data
     */
    public function testSetViewData()
    {
        $this->_oView->setViewData( array('1a', '2b') );
        $this->assertEquals( array('1a', '2b'), $this->_oView->getViewData() );
    }

    /*
     * Test get view data component
     */
    public function testGetViewDataElement()
    {
        $this->_oView->setViewData( array('aa'=>'aaValue', 'bb'=>'bbValue') );
        $this->assertEquals( 'aaValue', $this->_oView->getViewDataElement('aa') );
    }

    /*
     * Test set/get class location
     */
    public function testClassLocation()
    {
        $this->_oView->setClassLocation( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getClassLocation() );
    }

    /*
     * Test set/get view action
     */
    public function testThisAction()
    {
        $this->_oView->setThisAction( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getThisAction() );
    }

    /*
     * Test set/get parent
     */
    public function testParent()
    {
        $this->_oView->setParent( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getParent() );
    }

    /*
     * Test set/get is component
     */
    public function testIsComponent()
    {
        $this->_oView->setIsComponent( '123456789' );
        $this->assertEquals( '123456789', $this->_oView->getIsComponent() );
    }

    /**
     * Testing function execution code
     */
    public function testExecuteFunction()
    {
        $oView = $this->getMock( 'modOxView', array( 'xxx', '_executeNewAction' ) );
        $oView->expects( $this->once() )->method( 'xxx' )->will( $this->returnValue( 'xxx' ) );
        $oView->expects( $this->once() )->method( '_executeNewAction' )->with( $this->equalTo( 'xxx' ) );
        $oView->executeFunction( 'xxx' );
    }

    public function testExecuteFunctionExecutesComponentFunction()
    {
        $oCmp = $this->getMock( 'oxcmp_categories', array( 'xxx' ) );
        $oCmp->expects( $this->never() )->method( 'xxx' );
        $this->assertNull( $oCmp->executeFunction( 'yyy' ) );
    }

    public function testExecuteFunctionThrowsExeption()
    {
        $oView = $this->getMock( 'modOxView', array( 'xxx' ) );
        $oView->expects( $this->never() )->method( 'xxx' );


        try {
            $oView->executeFunction( 'yyy' );
        } catch(oxSystemComponentException $oEx) {
            $this->assertEquals( "ERROR_MESSAGE_SYSTEMCOMPONENT_FUNCTIONNOTFOUND", $oEx->getMessage() );
            return;
        }

        $this->fail("No exception thrown by executeFunction");
    }

    public function testExecuteFunctionExecutesOnlyOnce()
    {
        $oCmp = $this->getMock( 'oxcmp_categories', array( 'xxx' ) );
        $oCmp->expects( $this->once() )->method( 'xxx' );

        $oCmp->executeFunction( 'xxx' );
        $oCmp->executeFunction( 'xxx' );
    }


    /**
     * New action url getter tests
     */
    public function testExecuteNewActionNonSsl()
    {
        oxAddClassModule( "oxUtilsRedirectForoxviewTest", "oxutils" );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl' ) );
        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->at( 1 ) )->method( 'getConfigParam')->will( $this->returnValue( 'oxid.php' ) );
        $oConfig->expects( $this->once() )->method( 'isSsl')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->never() )->method( 'getSslShopUrl' );
        $oConfig->expects( $this->once() )->method( 'getShopUrl' )->will( $this->returnValue( 'shopurl/' ) );

        $oView = $this->getMock( 'oxview', array( 'getConfig' ) );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $sUrl = $oView->UNITexecuteNewAction( "testAction" );
        $this->assertEquals( 'shopurl/index.php?cl=testAction&'.oxSession::getInstance()->sid(), oxUtils::getInstance()->sRedirectUrl );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl' ) );
        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->at( 1 ) )->method( 'getConfigParam')->will( $this->returnValue( 'oxid.php' ) );
        $oConfig->expects( $this->once() )->method( 'isSsl')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->never() )->method( 'getSslShopUrl' );
        $oConfig->expects( $this->once() )->method( 'getShopUrl' )->will( $this->returnValue( 'shopurl/' ) );

        $oView = $this->getMock( 'oxview', array( 'getConfig' ) );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $sUrl = $oView->UNITexecuteNewAction( "testAction?someparam=12" );
        $this->assertEquals( "shopurl/index.php?cl=testAction&someparam=12&".oxSession::getInstance()->sid(), oxUtils::getInstance()->sRedirectUrl );

    }

    public function testExecuteNewActionSsl()
    {
        oxAddClassModule( "oxUtilsRedirectForoxviewTest", "oxutils" );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam', 'isSsl', 'getSslShopUrl', 'getShopUrl' ) );
        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->at( 1 ) )->method( 'getConfigParam')->will( $this->returnValue( 'oxid.php' ) );
        $oConfig->expects( $this->once() )->method( 'isSsl')->will( $this->returnValue( true ) );
        $oConfig->expects( $this->once() )->method( 'getSslShopUrl' )->will( $this->returnValue( 'SSLshopurl/' ) );
        $oConfig->expects( $this->never() )->method( 'getShopUrl' );

        $oView = $this->getMock( 'oxview', array( 'getConfig' ) );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $sUrl = $oView->UNITexecuteNewAction( "details?fnc=somefnc&anid=someanid" );
        $this->assertEquals( 'SSLshopurl/index.php?cl=details&fnc=somefnc&anid=someanid&'.oxSession::getInstance()->sid(), oxUtils::getInstance()->sRedirectUrl );
    }

    public function testExecuteNewActionSslIsAdmin()
    {
        oxAddClassModule( "oxUtilsRedirectForoxviewTest", "oxutils" );

        $oConfig = $this->getMock( 'oxconfig', array( 'isSsl', 'getSslShopUrl', 'getShopUrl' ) );
        $oConfig->expects( $this->once() )->method( 'isSsl')->will( $this->returnValue( true ) );
        $oConfig->expects( $this->once() )->method( 'getSslShopUrl' )->will( $this->returnValue( 'SSLshopurl/' ) );
        $oConfig->expects( $this->never() )->method( 'getShopUrl' );
        $oConfig->setConfigParam( 'sAdminDir', 'admin' );

        $oView = $this->getMock( 'oxview', array( 'getConfig', 'isAdmin' ) );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $oView->expects( $this->once() )->method( 'isAdmin' )->will( $this->returnValue( true ) );
        $sUrl = $oView->UNITexecuteNewAction( "details?fnc=somefnc&anid=someanid" );
        $this->assertEquals( 'SSLshopurl/admin/index.php?cl=details&fnc=somefnc&anid=someanid&'.oxSession::getInstance()->sid(), oxUtils::getInstance()->sRedirectUrl );
    }

    public function testGetTrustedShopIdNotValid()
    {
        $oView = $this->getProxyClass( 'oxview' );
        modConfig::getInstance()->setConfigParam( 'tsSealActive', 1 );
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', array (0=>'aaa') );

        $this->assertFalse( $oView->getTrustedShopId() );
    }

    public function testGetTrustedShopIdIfNotMultilanguage()
    {
        $oView = $this->getProxyClass( 'oxview' );
        modConfig::getInstance()->setConfigParam( 'tsSealActive', 1 );
        modConfig::getInstance()->setConfigParam( 'tsSealType', array( 0 => 'CLASSIC'));
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', 'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' );
        $this->assertEquals( 'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', $oView->getTrustedShopId() );
    }

    public function testGetTrustedShopIdIfNotMultilanguageNotValid()
    {
        $oView = $this->getProxyClass( 'oxview' );
        modConfig::getInstance()->setConfigParam( 'tsSealActive', 1 );
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', 'XXX' );
        $this->assertFalse( $oView->getTrustedShopId() );
    }

    public function testGetTrustedShopId()
    {
        $oView = $this->getProxyClass( 'oxview' );
        modConfig::getInstance()->setConfigParam( 'tsSealActive', 1 );
        modConfig::getInstance()->setConfigParam( 'tsSealType', array( 0 => 'CLASSIC'));
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', array (0=>'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA') );

        $this->assertEquals( 'XAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', $oView->getTrustedShopId() );
    }

    public function testGetTrustedShopIdNotActive()
    {
        $oView = $this->getProxyClass( 'oxview' );
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', null );

        $this->assertFalse( $oView->getTrustedShopId() );
    }

    public function testGetCharSet()
    {
        $oView = $this->getProxyClass( 'oxview' );
        $this->assertEquals( 'ISO-8859-15', $oView->getCharSet() );
    }

    public function testGetShopVersion()
    {
        $oView = $this->getProxyClass( 'oxview' );
        $this->assertEquals( modConfig::getInstance()->getActiveShop()->oxshops__oxversion->value, $oView->getShopVersion() );
    }

    public function testIsDemoVersion()
    {
        $oView = $this->getProxyClass( 'oxview' );
        if ( modConfig::getInstance()->detectVersion() == 1 ) {
            $this->assertTrue( $oView->isDemoVersion() );
        } else {
            $this->assertFalse( $oView->isDemoVersion() );
        }
    }

    public function testEditionIsNotEmpty()
    {
        //edition is always set
        $oView = $this->getProxyClass( 'oxview' );
        $sEdition = $oView->getShopEdition();
        $this->assertNotSame('', $sEdition);
    }

    public function testGetEdition()
    {
        //edition is always set
        $oView = $this->getProxyClass( 'oxview' );
        $sEdition = $oView->getShopEdition();

            $this->assertTrue($sEdition == "CE" || $sEdition == "PE");

    }


    public function testFullEditionIsNotEmpty()
    {
        //edition is always set
        $oView = $this->getProxyClass( 'oxview' );
        $sEdition = $oView->getShopFullEdition();
        $this->assertNotSame('', $sEdition);
    }

    public function testGetFullEdition()
    {
        //edition is always set
        $oView = $this->getProxyClass( 'oxview' );
        $sEdition = $oView->getShopFullEdition();

            $this->assertTrue($sEdition == "Community Edition" || $sEdition == "Professional Edition");

    }

    public function testShowNewsletter()
    {
        $oView = $this->getProxyClass( 'oxview' );
        $this->assertEquals( 1, $oView->showNewsletter() );
    }

    public function testSetShowNewsletter()
    {
        $oView = $this->getProxyClass( 'oxview' );
        $oView->setShowNewsletter(0);

        $this->assertEquals( 0, $oView->showNewsletter() );
    }

    public function testSetGetShopLogo()
    {
        $oView = $this->getProxyClass( 'oxview' );
        $oView->setShopLogo("testlogo");

        $this->assertEquals( "testlogo", $oView->getShopLogo() );
    }

    public function testSetGetActCategory()
    {
        $oView = new oxview();
        $oView->setActCategory( 'oClickCat' );
        $this->assertEquals( 'oClickCat', $oView->getActCategory() );
    }

    /**
     * Testing special getters setters
     */
    public function testGetCategoryIdAndSetCategoryId()
    {
        $oView = new oxview();
        $this->assertNull( $oView->getCategoryId() );

        modConfig::setParameter( 'cnid', 'xxx' );
        $this->assertEquals( 'xxx', $oView->getCategoryId() );

        // additionally checking cache
        modConfig::setParameter( 'cnid', null );
        $this->assertEquals( 'xxx', $oView->getCategoryId() );

        $oView->setCategoryId( 'yyy' );
        $this->assertEquals( 'yyy', $oView->getCategoryId() );
    }

    public function testGetActionClassName()
    {
        $oView = $this->getMock( 'oxview', array( 'getClassName' ) );
        $oView->expects( $this->once() )->method( 'getClassName')->will( $this->returnValue( 'className' ) );

        $this->assertEquals( 'className', $oView->getActionClassName() );
    }

    /**
     * Testing getter for checking if user is connected using Facebook connect
     *
     * return null
     */
    public function testIsConnectedWithFb()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return true;}" );

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "bl_showFbConnect", false );

        $oView = new oxView();
        $this->assertFalse( $oView->isConnectedWithFb() );

        $myConfig->setConfigParam( "bl_showFbConnect", true );
        $this->assertTrue( $oView->isConnectedWithFb() );

        oxTestModules::addFunction( "oxFb", "isConnected", "{return false;}" );
        $this->assertFalse( $oView->isConnectedWithFb() );
    }

    /**
     * Testing getting connected with Facebook connect user id
     *
     * return null
     */
    public function testGetFbUserId()
    {
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123;}" );

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "bl_showFbConnect", false );

        $oView = new oxView();
        $this->assertNull( $oView->getFbUserId() );

        $myConfig->setConfigParam( "bl_showFbConnect", true );
        $this->assertEquals( "123", $oView->getFbUserId() );
    }

    /**
     * Testing getting true or false for showing popup after user
     * connected using Facebook connect - FB connect is disabled
     *
     * return null
     */
    public function testShowFbConnectToAccountMsg_FbConnectIsOff()
    {
        $myConfig = modConfig::getInstance();
        $myConfig->setParameter( "fblogin", false );

        $oView = new oxView();
        $this->assertFalse( $oView->showFbConnectToAccountMsg() );
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
        $myConfig = modConfig::getInstance();
        $myConfig->setParameter( "fblogin", true );

        $oView = $this->getMock( 'oxview', array( 'getUser' ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( null ) );

        $this->assertTrue( $oView->showFbConnectToAccountMsg() );
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
        $myConfig = modConfig::getInstance();
        $myConfig->setParameter( "fblogin", true );
        $oUser = new oxUser();

        $oView = $this->getMock( 'oxview', array( 'getUser' ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );

        $this->assertFalse( $oView->showFbConnectToAccountMsg() );
    }

    /**
     * Testing mall mode getter
     */
    public function testIsMall()
    {
        $oView = new oxview();
            $this->assertFalse( $oView->isMall() );
    }

    public function testIsCallForCache()
    {
        $oView = new oxview();
        $oView->setIsCallForCache( '123456789' );
        $this->assertEquals( '123456789', $oView->getIsCallForCache() );
    }
    
    /*
     * Testing oxview::getViewId()
     * 
     * @return null
     */
    public function testgetViewId()
    {
        $oView = new oxView();
        $this->assertNull( $oView->getViewId() );        
    }

    public function testShowRdfa()
    {
        $oView = new oxview();
        $this->assertFalse( $oView->showRdfa() );
    }

}
