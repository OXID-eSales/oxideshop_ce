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

class Unit_Core_oxVersionCheckerTest extends OxidTestCase
{

    /**
     * Testing version getter and setter
     */
    public function testGetVersion()
    {
        $oChecker = oxNew( "oxVersionChecker" );
        $oChecker->setVersion( "v123" );

        $this->assertEquals( "v123",  $oChecker->getVersion() );
    }

    /**
     * Testing edition getter and setter
     */
    public function testGetEdition()
    {
        $oChecker = oxNew( "oxVersionChecker" );
        $oChecker->setEdition( "e123" );

        $this->assertEquals( "e123",  $oChecker->getEdition() );
    }

    /**
     * Testing revision getter and setter
     */
    public function testGetRevision()
    {
        $oChecker = oxNew( "oxVersionChecker" );
        $oChecker->setRevision( "r123" );

        $this->assertEquals( "r123",  $oChecker->getRevision() );
    }

    /**
     * Testing base directory getter and setter
     */
    public function testGetBaseDirectory()
    {
        $oChecker = oxNew( "oxVersionChecker" );
        $oChecker->setBaseDirectory( "somedir" );

        $this->assertEquals( "somedir",  $oChecker->getBaseDirectory() );
    }

    /**
     * Testing home link getter and setter
     */
    public function testGetHomeLink()
    {
        $oChecker = oxNew( "oxVersionChecker" );
        $oChecker->setHomeLink( "someurl" );

        $this->assertEquals( "someurl",  $oChecker->getHomeLink() );
    }

    /**
     * Testing revision getter and setter
     */
    public function testGetListAllFiles()
    {
        $oChecker = oxNew( "oxVersionChecker" );

        $oChecker->setListAllFiles( true );
        $this->assertTrue( $oChecker->getListAllFiles() );

        $oChecker->setListAllFiles( false );
        $this->assertFalse( $oChecker->getListAllFiles() );
    }


}