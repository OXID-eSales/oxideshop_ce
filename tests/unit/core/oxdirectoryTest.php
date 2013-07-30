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

class Unit_Core_oxDirectoryTest extends OxidTestCase
{

    /**
     * Testing file list validation method     *
     */
    public function testFileExists()
    {
        $oDirReader = oxNew( "oxDirectory" );
        $oDirReader->setBaseDirectory( oxConfig::getInstance()->getConfigParam( "sShopDir") );

        $this->assertTrue( $oDirReader->fileExists("bin/cron.php") );
        $this->assertFalse( $oDirReader->fileExists("bin/cron.log") );
    }

    /**
     * Testing file list validation method     *
     */
    public function testGetDirectoryFiles()
    {
        $oDirReader = oxNew( "oxDirectory" );
        $oDirReader->setBaseDirectory( oxConfig::getInstance()->getConfigParam( "sShopDir") );

        $aResultExistingPHP = $oDirReader->getDirectoryFiles( 'bin/', array( 'php', 'tpl' ) );
        $aResultExistingAll = $oDirReader->getDirectoryFiles( 'bin/' );

        $this->assertEquals( 1, count($aResultExistingPHP) );
        $this->assertContains( 'bin/cron.php', $aResultExistingPHP );

        $this->assertEquals( 3, count($aResultExistingAll) );
        $this->assertContains( 'bin/.htaccess', $aResultExistingAll );
        $this->assertContains( 'bin/cron.php',  $aResultExistingAll );
        $this->assertContains( 'bin/log.txt',   $aResultExistingAll );

    }
}