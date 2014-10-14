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

}
