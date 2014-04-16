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

class oxUtilsRedirect extends oxUtils
{
    public $sRedirectUrl = null;

    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301 )
    {
        $this->sRedirectUrl = $sUrl;
    }
}

/**
 * Testing oxstart class
 */
class Unit_Views_oxstartTest extends OxidTestCase
{
    public function testRenderNormal()
    {
        $oStart = new oxStart();
        $oStart->getConfig();
        $sRes = $oStart->render();
        $this->assertEquals('message/err_unknown.tpl', $sRes);
    }



    public function testGetErrorNumber()
    {
        $oStart = $this->getProxyClass( 'oxstart' );
        $this->setRequestParam( 'errornr', 123 );

        $this->assertEquals( 123, $oStart->getErrorNumber() );
    }


    public function testPageCloseNoSess()
    {
        $oStart = $this->getMock( 'oxstart', array('getSession') );
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue(null));

        $oUtils = $this->getMock( 'oxUtils', array('commitFileCache') );
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals( null, $oStart->pageClose() );
    }


    public function testPageClose()
    {
        $oSess = $this->getMock( 'stdclass', array('freeze') );
        $oSess->expects($this->once())->method('freeze')->will($this->returnValue(null));

        $oStart = $this->getMock( 'oxstart', array('getSession') );
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue($oSess));

        $oUtils = $this->getMock( 'oxUtils', array('commitFileCache') );
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals( null, $oStart->pageClose() );
    }

    public function testAppInitUnlicensed()
    {
            return ;

        oxAddClassModule("oxUtilsRedirect", "oxutils");
        $this->setConfigParam( 'redirected', 1 );

        $oSerial = $this->getMock( 'oxserial', array( 'isUnlicensedSerial' ) );
        $oSerial->expects( $this->atLeastOnce() )->method( 'isUnlicensedSerial')->will( $this->returnValue( true ) );

        $oConfig = $this->getMock( 'oxconfig', array( 'isProductiveMode', 'getSerial', 'isAdmin' ) );
        $oConfig->expects( $this->any() )->method( 'isProductiveMode')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->once() )->method( 'getSerial')->will( $this->returnValue( $oSerial ) );
        $oConfig->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $oConfig->setConfigParam( 'blBackTag', 0 );
        $oConfig->setConfigParam( 'sTagList', time() * 2 );
        $oConfig->setConfigParam( 'blShopStopped', true );
        $oConfig->setConfigParam( 'IMS', time() );
        oxRegistry::set("oxconfig", $oConfig);

        $sHomeUrl = $oConfig->getShopUrl();
        $this->assertEquals(true, $oConfig->getShopConfVar('blShopStopped'));
        $this->assertEquals(true, $oConfig->getShopConfVar('blBackTag'));
        $this->assertEquals($sHomeUrl . 'offline.html', oxUtils::getInstance()->sRedirectUrl);
    }

    public function testAppInitUnlicensedPE()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{ return 6; }');
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception( $aA[0] ); }');

        $this->setConfigParam( 'redirected', 1 );
        $this->setConfigParam( 'cl', 'someClass' );

        $oSerial = $this->getMock( 'oxserial', array( 'isUnlicensedSerial' ) );
        $oSerial->expects( $this->atLeastOnce() )->method( 'isUnlicensedSerial')->will( $this->returnValue( true ) );

        $oConfig = $this->getMock( 'oxconfig', array( 'isProductiveMode', 'getSerial', 'isAdmin' ) );
        $oConfig->expects( $this->any() )->method( 'isProductiveMode' )->will( $this->onConsecutiveCalls( false ) );
        $oConfig->expects( $this->any() )->method( 'getSerial')->will( $this->returnValue( $oSerial ) );
        $oConfig->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        try {
            oxRegistry::set("oxconfig", $oConfig);
            $oConfig->getShopUrl();
        } catch ( exception $oExcp ) {
            $sHomeUrl = $oConfig->getShopUrl();
            $this->assertEquals(true, $oConfig->getShopConfVar('blShopStopped'));
            $this->assertEquals(true, $oConfig->getShopConfVar('blBackTag'));
            $this->assertEquals($sHomeUrl.  'offline.html', $oExcp->getMessage());
            return;
        }
        $this->fail( 'error in testAppInitUnlicensedPE' );
    }
}
