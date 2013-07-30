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
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxCurlTest extends OxidTestCase
{

    public function testGetWebServiceRequestURL()
    {
        $oCurl = oxNew( "oxCurl" );

        $this->assertFalse( $oCurl->callWebService() );

        $oCurl->setWebServiceURL( "www.google.com" );

        $this->assertEquals( "www.google.com?", $oCurl->getWebServiceRequestURL() );

        $oCurl->setWebServiceParams( array( "param1" => "val1", "param2" => "val2" ) );

        $this->assertEquals( "www.google.com?param1=val1&param2=val2", $oCurl->getWebServiceRequestURL() );
        $this->assertEquals( "www.google.com?param3=val3&param4=val4", $oCurl->getWebServiceRequestURL( array( "param3" => "val3", "param4" => "val4" ) ) );
    }
}