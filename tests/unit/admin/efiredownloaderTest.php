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
 * Tests for EFire_Downloader class
 */
class Unit_Admin_EFireDownloaderTest extends OxidTestCase
{
    /**
     * EFire_Downloader::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new EFire_Downloader();
        $this->assertEquals( 'efire_downloader.tpl', $oView->render() );
    }

    /**
     * EFire_Downloader::GetAdminList() test case
     *
     * @return null
     */
    public function testGetAdminList()
    {
        oxTestModules::addFunction( 'oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights->value = "malladmin"; }');

        $oView = new EFire_Downloader();
        $oAdminUserList = $oView->getAdminList();
        $aUserList = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getAll( "select oxid from oxuser" );

        foreach ( $aUserList as $iKey => $aUserData ) {
            $this->assertTrue( $oAdminUserList->offsetExists( $aUserData['oxid'] ) );
        }
    }

    /**
     * EFire_Downloader::GetConnector() test case
     *
     * @return null
     */
    public function testGetConnector()
    {
        oxTestModules::addFunction( 'oxefidownloader', 'downloadConnector', '{}');

        $oView = new EFire_Downloader();
        $oView->getConnector();
        $this->assertEquals( oxLang::getInstance()->translateString('EFIRE_DOWNLOADER_SUCCESS' ), $oView->getViewDataElement( "message" ) );
    }

    /**
     * EFire_Downloader::GetConnector() test case
     *
     * @return null
     */
    public function testGetConnectorErrorFetchingConnector()
    {
        oxTestModules::addFunction( 'oxefidownloader', 'downloadConnector', '{ throw new Exception( "getConnector" );}');
        oxTestModules::addFunction( 'oxUtilsView', 'addErrorToDisplay', '{ throw new Exception( "addErrorToDisplay" );}');

        try {
            $oView = new EFire_Downloader();
            $oView->getConnector();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "addErrorToDisplay", $oExcp->getMessage(), "Error in EFire_Downloader::getConnector()" );
            return;
        }
        $this->fail( "Error in EFire_Downloader::getConnector()" );
    }
}
