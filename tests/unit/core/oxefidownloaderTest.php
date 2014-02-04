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

class Unit_Core_oxEfiDownloaderTest extends OxidTestCase
{

    protected $_sDownloadedFileName = "testConnector";

    protected $_sDownloadedFileContents = "I am the connector";

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxconfig where oxvarname = 'sEfiUsername' || oxvarname = 'sEfiPassword'";
        oxDb::getDb()->execute($sQ);
        @unlink(getShopBasePath() . "/oxefi.php");
        parent::tearDown();
    }

    public function testNonTestVersion()
    {
        $oEfiDownloader = new oxEfiDownloader();
        $sEfireSoapUrl = EFIRE_WSDL_URL;
        $this->assertNotContains('efire-linux', $sEfireSoapUrl);
    }

    public function testDownloadConnector()
    {
        //not too much to test as all the work is done over SOAP
        $oSubj = $this->getMock('oxEfiDownloader', array('_getConnectorClassName', '_getConnectorContents', '_init'));
        $oSubj->expects($this->once())->method('_getConnectorClassName')->will($this->returnValue($this->_sDownloadedFileName));
        $oSubj->expects($this->once())->method('_getConnectorContents')->will($this->returnValue($this->_sDownloadedFileContents));
        $oSubj->expects($this->once())->method('_init')->will($this->returnValue(true));

        $sFullFileName = $oSubj->downloadConnector('someusername', 'somepassword', 'someversion', false);

        $sExptFileName = getShopBasePath(). "core/" . strtolower( $this->_sDownloadedFileName) . '.php';
        $this->assertEquals($sExptFileName, $sFullFileName);
        $this->assertTrue(file_exists($sFullFileName));

        $sExpFileContents = $this->_sDownloadedFileContents;
        $sContents = file_get_contents($sFullFileName);

        $this->assertEquals($sExpFileContents, $sContents);

        //cleaning up
        unlink($sFullFileName);
        //cleaned
        $this->assertFalse(file_exists($sFullFileName));
    }

    public function testDownloadConnectorRemovesOldFile()
    {
        $sFileName = getShopBasePath() . "/oxefi.php";
        file_put_contents($sFileName, "testConnector");
        //not too much to test as all the work is done over SOAP
        $oSubj = $this->getMock('oxEfiDownloader', array('_getConnectorClassName', '_getConnectorContents', '_init'));
        $oSubj->expects($this->once())->method('_getConnectorClassName')->will($this->returnValue($this->_sDownloadedFileName));
        $oSubj->expects($this->once())->method('_getConnectorContents')->will($this->returnValue($this->_sDownloadedFileContents));
        $oSubj->expects($this->once())->method('_init')->will($this->returnValue(true));

        $sFullFileName = $oSubj->downloadConnector('someusername', 'somepassword', 'someversion', false);

        $this->assertFalse(file_exists($sFileName));
    }

    public function testDownloadConnectorSaveCredentials()
    {
        $oSubj = $this->getMock('oxEfiDownloader', array('_getConnectorClassName', '_getConnectorContents', '_init'));
        $oSubj->expects($this->once())->method('_getConnectorClassName')->will($this->returnValue($this->_sDownloadedFileName));
        $oSubj->expects($this->once())->method('_getConnectorContents')->will($this->returnValue($this->_sDownloadedFileContents));
        $oSubj->expects($this->once())->method('_init')->will($this->returnValue(true));

        $sFullFileName = $oSubj->downloadConnector('someusername', 'somepassword', 'someversion', true);

        $this->assertEquals('someusername', modConfig::getInstance()->getShopConfVar("sEfiUsername"));
        $this->assertEquals('somepassword', modConfig::getInstance()->getShopConfVar("sEfiPassword"));
    }

    public function testDownloadConnectorDoNotSaveCredentials()
    {
        $oSubj = $this->getMock('oxEfiDownloader', array('_getConnectorClassName', '_getConnectorContents', '_init'));
        $oSubj->expects($this->once())->method('_getConnectorClassName')->will($this->returnValue($this->_sDownloadedFileName));
        $oSubj->expects($this->once())->method('_getConnectorContents')->will($this->returnValue($this->_sDownloadedFileContents));
        $oSubj->expects($this->once())->method('_init')->will($this->returnValue(true));

        $sFullFileName = $oSubj->downloadConnector('someusername', 'somepassword', 'someversion', false);

        $this->assertEquals('', modConfig::getInstance()->getShopConfVar("sEfiUsername"));
        $this->assertEquals('', modConfig::getInstance()->getShopConfVar("sEfiPassword"));
    }

}
