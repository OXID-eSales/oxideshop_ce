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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';


class Unit_Core_oxWidgetControlTest extends OxidTestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        oxDb::getDb()->execute( "delete from oxlogs" );
         modDB::getInstance()->cleanup();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStart()
    {
        $oControl = $this->getMock( "oxWidgetControl", array( "_runOnce", "_runLast", "_process" ), array(), '', false );
        $oControl->expects( $this->once() )->method( '_runOnce' );
        $oControl->expects( $this->once() )->method( '_runLast' );
        $oControl->expects( $this->once() )->method( '_process' )->with( $this->equalTo( "start" ), $this->equalTo( "testFnc" ), $this->equalTo( "testParams" ), $this->equalTo( "testViewsChain" ) );
        $oControl->start("start", "testFnc", "testParams", "testViewsChain");
     }

    /**
     * Testing oxShopControl::_runOnce()
     *
     * @return null
     */
    public function testRunOnce()
    {
            return; // EE only

        // if _runOnce() will be executed, this param will be set to true
        $this->setSessionParam( "blRunOnceExecuted", false );

        $oReverseProxyBackend = $this->getMock( "stdClass", array( "isActive" ) );
        $oReverseProxyBackend->expects( $this->any() )->method( 'isActive' )->will( $this->returnValue(true) );

        OxRegistry::set( "oxReverseProxyBackend", $oReverseProxyBackend );

        $oConfig = $this->getMock( "oxConfig", array( "hasActiveViewsChain", "isProductiveMode" ) );
        $oConfig->expects( $this->any() )->method( 'hasActiveViewsChain' )->will( $this->returnValue(false) );
        $oConfig->expects( $this->any() )->method( 'isProductiveMode' )->will( $this->returnValue(true) );

        $oControl = $this->getMock( "oxWidgetControl", array( "getConfig" ) );
        $oControl->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue($oConfig) );

        $oControl->blSkipForTest = true;
        $oControl->UNITrunOnce();

        $this->assertEquals( true, $this->getSessionParam("blRunOnceExecuted") );
     }

    /**
     * Testing oxShopControl::_runOnce()
     *
     * @return null
     */
    public function testRunOnce_reverseProxyIsNotActive()
    {
            return; // EE only

        // if _runOnce() will be executed, this param will be set to true
        $this->setSessionParam( "blRunOnceExecuted", false );

        $oReverseProxyBackend = $this->getMock( "stdClass", array( "isActive" ) );
        $oReverseProxyBackend->expects( $this->any() )->method( 'isActive' )->will( $this->returnValue(false) );

        OxRegistry::set( "oxReverseProxyBackend", $oReverseProxyBackend );

        $oConfig = $this->getMock( "oxConfig", array( "hasActiveViewsChain" ) );
        $oConfig->expects( $this->any() )->method( 'hasActiveViewsChain' )->will( $this->returnValue(false) );

        $oControl = $this->getMock( "oxWidgetControl", array( "getConfig" ) );
        $oControl->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue($oConfig) );

        $oControl->blSkipForTest = true;
        $oControl->UNITrunOnce();

        $this->assertEquals( false, $this->getSessionParam("blRunOnceExecuted") );
     }

    /**
     * Testing oxShopControl::_runOnce()
     *
     * @return null
     */
    public function testRunOnce_hasActiveViewsChain()
    {
            return; // EE only

        // if _runOnce() will be executed, this param will be set to true
        $this->setSessionParam( "blRunOnceExecuted", false );

        $oReverseProxyBackend = $this->getMock( "stdClass", array( "isActive" ) );
        $oReverseProxyBackend->expects( $this->any() )->method( 'isActive' )->will( $this->returnValue(true) );

        OxRegistry::set( "oxReverseProxyBackend", $oReverseProxyBackend );

        $oConfig = $this->getMock( "oxConfig", array( "hasActiveViewsChain" ) );
        $oConfig->expects( $this->any() )->method( 'hasActiveViewsChain' )->will( $this->returnValue(true) );

        $oControl = $this->getMock( "oxWidgetControl", array( "getConfig" ) );
        $oControl->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue($oConfig) );

        $oControl->blSkipForTest = true;
        $oControl->UNITrunOnce();

        $this->assertEquals( false, $this->getSessionParam("blRunOnceExecuted") );
    }

    /**
     * Testing oxShopControl::_runLast()
     *
     * @return null
     */
    public function testRunLast()
    {
        $oConfig = $this->getMock( "oxConfig", array( "hasActiveViewsChain" ) );
        $oConfig->expects( $this->any() )->method( 'hasActiveViewsChain' )->will( $this->returnValue(true) );

        $oConfig->setActiveView( "testView1" );
        $oConfig->setActiveView( "testView2" );

        $this->assertEquals( array("testView1", "testView2"),$oConfig->getActiveViewsList() );


        $oControl = $this->getMock( "oxWidgetControl", array( "getConfig" ) );
        $oControl->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue($oConfig) );

        $oControl->UNITrunLast();

        $this->assertEquals( array("testView1"), $oConfig->getActiveViewsList() );
        $this->assertEquals( "testView1", oxRegistry::get("oxUtilsView")->getSmarty()->get_template_vars("oView") );
    }

    /**
     * Testing oxShopControl::_initializeViewObject()
     *
     * @return null
     */
    public function testInitializeViewObject()
    {
        $oControl = oxNew( "oxWidgetControl" );
        $oView = $oControl->UNITinitializeViewObject("oxwCookieNote", "testFunction", array("testParam"=>"testValue"));

        //checking widget object
        $this->assertEquals( "oxwCookieNote", $oView->getClassName() );
        $this->assertEquals( "testFunction", $oView->getFncName() );
        $this->assertEquals( "testValue", $oView->getViewParameter("testParam") );

        // checking active view object
        $this->assertEquals( 1, count($oControl->getConfig()->getActiveViewsList()) );
        $this->assertEquals( "oxwCookieNote", $oControl->getConfig()->getActiveView()->getClassName() );
    }

    /**
     * Testing oxShopControl::_initializeViewObject()
     *
     * @return null
     */
    public function testInitializeViewObject_hasViewChain()
    {
        $oControl = oxNew( "oxWidgetControl" );
        $oView = $oControl->UNITinitializeViewObject("oxwCookieNote", "testFunction", array("testParam"=>"testValue"), array("account", "oxubase") );

        //checking widget object
        $this->assertEquals( "oxwCookieNote", $oView->getClassName() );
        $this->assertEquals( "testFunction", $oView->getFncName() );
        $this->assertEquals( "testValue", $oView->getViewParameter("testParam") );

        // checking active view objects
        $aActiveViews = $oControl->getConfig()->getActiveViewsList();

        $this->assertEquals( 3, count($aActiveViews) );
        $this->assertEquals( "account",     $aActiveViews[0]->getClassName() );
        $this->assertEquals( "oxUBase",     get_class($aActiveViews[1]) );
        $this->assertEquals( "oxwCookieNote", $aActiveViews[2]->getClassName() );

        $this->assertEquals( "oxwCookieNote", $oControl->getConfig()->getActiveView()->getClassName() );
    }

}
