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

class Unit_Core_oxDiagnosticsTest extends OxidTestCase
{

    /**
     * Testing FileCheckerPathList getter and setter
     */
    public function testGetFileCheckerPathList()
    {
        $oDiagnostics = oxNew( "oxDiagnostics" );
        $oDiagnostics->setFileCheckerPathList( array( "admin", "views" ) );

        $this->assertEquals( 2,  count( $oDiagnostics->getFileCheckerPathList() ) );
        $this->assertContains( "admin",  $oDiagnostics->getFileCheckerPathList() );
        $this->assertContains( "views",  $oDiagnostics->getFileCheckerPathList() );
    }

    /**
     * Testing FileCheckerPathList getter and setter
     */
    public function testGetFileCheckerExtensionList()
    {
        $oDiagnostics = oxNew( "oxDiagnostics" );
        $oDiagnostics->setFileCheckerExtensionList( array( "ex1", "ex2" ) );

        $this->assertEquals( 2,  count( $oDiagnostics->getFileCheckerExtensionList() ) );
        $this->assertContains( "ex1",  $oDiagnostics->getFileCheckerExtensionList() );
        $this->assertContains( "ex2",  $oDiagnostics->getFileCheckerExtensionList() );
    }


}