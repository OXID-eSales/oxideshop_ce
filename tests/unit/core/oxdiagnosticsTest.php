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

    protected function _setUpTestGetShopDetails()
    {
        $oDb = oxDb::getDb();
        $oDb->execute( "DELETE FROM `oxshops` WHERE `oxid` > 1" );

        for ( $i = 2; $i < 5; $i++ ) {
            $oDb->execute( "INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME) VALUES ($i, ".($i%2).", 'Test Shop $i')" );
        }

        $oDb->execute( "DELETE FROM `oxcategories`" );

        for ( $i = 3; $i < 12; $i++ ) {
            $oDb->execute( "Insert into oxcategories (`OXID`,`OXROOTID`,`OXLEFT`,`OXRIGHT`,`OXSHOPINCL`,`OXTITLE`,`OXACTIVE`,`OXPRICEFROM`,`OXPRICETO`)" .
            "values ('test".$i."','test','1','4','1','test',".($i%2).",'10','50')" );
        }

        $this->getDb()->execute("delete from `oxarticles` ");
        for ( $i = 2; $i < 9; $i++ ) {
            $oDb->execute( "INSERT INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM` ) VALUES ".
                   "('_testArtId".$i."', 'oxbaseshop', '', ".($i%2).", '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0802-85-823-7-1')" );
        }

        $this->getDb()->execute("delete from `oxuser` ");
        for ( $i = 2; $i < 11; $i++ ) {
            $oDb->execute( "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXDISABLEAUTOGRP`, `OXLDAPKEY`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`, `OXFBID`, `OXTIMESTAMP`) ".
            " VALUES ('test_id".$i."', ".($i%2).", '', '1', 'test".$i."', '', '', NULL, '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '0000-00-00', '', '0', '', '0', '', '0', '0', '0', CURRENT_TIMESTAMP)" );
        }
    }

    public function testGetShopDetails()
    {
        $this->_setUpTestGetShopDetails();

        $oDiagnostics = oxNew( 'oxDiagnostics' );

        $oDiagnostics->setShopLink( 'someShopURL' );
        $oDiagnostics->setEdition( 'someEdition' );
        $oDiagnostics->setVersion( 'someVersion' );
        $oDiagnostics->setRevision( 'someRevision' );

        $aResult = $oDiagnostics->getShopDetails();

        $this->assertEquals( 12, count($aResult) );
        $this->assertEquals( 'someShopURL', $aResult['URL']	 );
        $this->assertEquals( 'someEdition', $aResult['Edition'] );
        $this->assertEquals( 'someVersion', $aResult['Version'] );
        $this->assertEquals( 'someRevision', $aResult['Revision'] );
        $this->assertEquals( 4, $aResult['Subshops (Total)'] );
        $this->assertEquals( 2, $aResult['Subshops (Active)'] );
        $this->assertEquals( 9, $aResult['Categories (Total)'] );
        $this->assertEquals( 5, $aResult['Categories (Active)'] );
        $this->assertEquals( 7, $aResult['Articles (Total)'] );
        $this->assertEquals( 3, $aResult['Articles (Active)'] );
        $this->assertEquals( 9, $aResult['Users (Total)'] );


    }


}