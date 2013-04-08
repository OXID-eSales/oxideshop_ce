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
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for dyn_trusted class
 */
class Unit_Admin_dyntrustedTest extends OxidTestCase
{
    /**
     * dyn_trusted::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_trusted();
        $this->assertEquals( 'dyn_trusted.tpl', $oView->render() );
    }

    /**
     * dyn_trusted::Save() test case
     *
     * @return null
     */
    public function testSaveNothingToSave()
    {
        modConfig::setParameter( "aShopID_TrustedShops", array( "testValue" ) );

        $oView = $this->getMock( "dyn_trusted", array( "_checkTsId", "getConfig" ), array(), '', false );
        $oView->expects( $this->once() )->method( '_checkTsId' );
        $oView->expects( $this->never() )->method( 'getConfig' );

        $oView->save();
        $this->assertEquals( "1", $oView->getViewDataElement( "errorsaving" ) );
        $this->assertNull( $oView->getViewDataElement( "aShopID_TrustedShops" ) );
    }

    /**
     * dyn_trusted::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        $oResults = new oxStdClass();
        $oResults->stateEnum = 'TEST';
        $oResults->typeEnum = 'EXCELLENCE';
        modConfig::setParameter( "aShopID_TrustedShops", array("test") );
        modConfig::setParameter( "aTsUser", 'testUser' );
        modConfig::setParameter( "aTsPassword", 'testPsw' );
        modConfig::setParameter( "tsSealActive", true );
        modConfig::setParameter( "tsTestMode", false );

        $oConfig = $this->getMock( "oxStdClass", array( "getShopId", "saveShopConfVar" ) );
        $oConfig->expects( $this->at(0) )->method( 'getShopId' )->will( $this->returnValue( "shopid" ) );
        $oConfig->expects( $this->at(1) )->method( 'saveShopConfVar' )->with( $this->equalTo( "aarr" ), $this->equalTo( "iShopID_TrustedShops" ), $this->equalTo( array("test") ), $this->equalTo( "shopid" ) );
        $oConfig->expects( $this->at(2) )->method( 'saveShopConfVar' )->with( $this->equalTo( "aarr" ), $this->equalTo( "aTsUser" ), $this->equalTo( 'testUser' ), $this->equalTo( "shopid" ) );
        $oConfig->expects( $this->at(3) )->method( 'saveShopConfVar' )->with( $this->equalTo( "aarr" ), $this->equalTo( "aTsPassword" ), $this->equalTo( 'testPsw' ), $this->equalTo( "shopid" ) );
        $oConfig->expects( $this->at(4) )->method( 'saveShopConfVar' )->with( $this->equalTo( "bool" ), $this->equalTo( "tsTestMode" ), $this->equalTo( false ), $this->equalTo( "shopid" ) );
        $oConfig->expects( $this->at(5) )->method( 'saveShopConfVar' )->with( $this->equalTo( "bool" ), $this->equalTo( "tsSealActive" ), $this->equalTo( true ), $this->equalTo( "shopid" ) );
        $oConfig->expects( $this->at(6) )->method( 'saveShopConfVar' )->with( $this->equalTo( "aarr" ), $this->equalTo( "tsSealType" ), $this->equalTo( array('EXCELLENCE') ), $this->equalTo( "shopid" ) );

        $oView = $this->getMock( "dyn_trusted", array( "getConfig", "_checkTsId" ), array(), '', false );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $oView->expects( $this->once() )->method( '_checkTsId' )->will( $this->returnValue( $oResults ) );

        $oView->save();
    }

    /**
     * dyn_trusted::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = new dyn_trusted();
        $this->assertEquals( 'dyn_interface', $oView->getViewId() );
    }
}
