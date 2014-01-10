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
 * @version   SVN: $Id: oxcmpCurTest.php 25505 2010-02-02 02:12:13Z alfonsas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * oxcmp_shop tests
 */
class Unit_Views_oxCmpShopTest extends OxidTestCase
{
    /**
     * Testing oxcmp_shop::render()
     *
     * @return null
     */
    public function testRenderNoActiveShop()
    {
        $sRedirUrl = oxConfig::getInstance()->getShopMainUrl().'offline.html';
        $this->setExpectedException('oxException', $sRedirUrl);

        $oView = $this->getMock( "oxStdClass", array( "getClassName" ) );
        $oView->expects( $this->once() )->method('getClassName')->will( $this->returnValue( "test" ) );

        $oShop = new oxStdClass();
        $oShop->oxshops__oxactive = new oxField( 0 );

        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new oxException($url);}');

        $oConfig = $this->getMock( "oxStdClass", array( "getConfigParam", "getActiveView", "getActiveShop" ) );
        $oConfig->expects( $this->once() )->method('getActiveView')->will( $this->returnValue( $oView ) );
        $oConfig->expects( $this->any() )->method('getConfigParam')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->once() )->method('getActiveShop')->will( $this->returnValue( $oShop ) );

        $oCmp = $this->getMock( "oxcmp_shop", array( "getConfig", "isAdmin" ), array(), '', false );
        $oCmp->expects( $this->once() )->method('getConfig')->will( $this->returnValue( $oConfig ) );
        $oCmp->expects( $this->once() )->method('isAdmin')->will( $this->returnValue( false ) );

        $oCmp->render();
    }

    /**
     * Testing oxcmp_shop::render()
     *
     * @return null
     */
    public function testRenderPE()
    {

        $sLogoPath = oxConfig::getInstance()->getConfigParam( "sShopDir" )."/out/azure/img/";

        $oShop = new oxShop();
        $oShop->oxshops__oxactive = new oxField( 1 );

        $oParent = $this->getMock( "oxStdClass", array( "setShopLogo" ) );
        $oParent->expects( $this->once() )->method('setShopLogo');

        $oConfig = $this->getMock( "oxStdClass", array( "getConfigParam", "getImageDir", "getActiveShop" ) );
        $oConfig->expects( $this->at( 0 ) )->method('getConfigParam')->with( $this->equalTo( "sShopLogo" ) )->will( $this->returnValue( "stars.jpg" ) );
        $oConfig->expects( $this->at( 1 ) )->method('getImageDir')->will( $this->returnValue( $sLogoPath ) );
        $oConfig->expects( $this->at( 2 ) )->method('getActiveShop')->will( $this->returnValue( $oShop ) );

        $oCmp = $this->getMock( "oxcmp_shop", array( "getConfig", "isAdmin" ), array(), '', false );
        $oCmp->expects( $this->once() )->method('getConfig')->will( $this->returnValue( $oConfig ) );
        $oCmp->setParent( $oParent );

        $this->assertTrue( $oCmp->render() instanceof oxShop );
    }
}

