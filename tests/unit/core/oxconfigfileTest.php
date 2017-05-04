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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_OxConfigFileTest extends OxidTestCase
{

    /**
     * Test for OxConfigFile::getVar() method
     */
    public function testGetVar()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");

        //taking random value from config.inc.php
        $sVar = $oConfigFile->getVar("sTsUser");
        $this->assertSame("oxid_esales", $sVar);
    }

    /**
     * Test for OxConfigFile::setVar() method
     */
    public function testSetVar()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");

        //taking random value from config.inc.php
        $oConfigFile->setVar("sTsUser", 'test_value');

        $sVar = $oConfigFile->getVar("sTsUser");
        $this->assertSame('test_value', $sVar);
    }

    /**
     * Tests OxConfigFile::isVarSet() method
     */
    public function testIsVarSet()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");
        $this->assertTrue($oConfigFile->isVarSet("sTsUser"), "Variable is supposed to be set");
        $this->assertFalse($oConfigFile->isVarSet("nonexistingVar"), "Variable is not supposed to be set");
    }

    /**
     * Test for OxConfigFile::getVars() method
     */
    public function testGetVars()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");
        $aVars = $oConfigFile->getVars();
        $this->assertArrayHasKey("sTsUser", $aVars);
        $this->assertEquals($aVars["sTsUser"], "oxid_esales");
        $this->assertTrue(count($aVars) > 10);
    }

    /**
     * Test for OxConfigFile::getVars() method, checks that internal vars are not returned
     */
    public function testGetVarsPublicOnly()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");
        $aVars = $oConfigFile->getVars();
        $this->assertArrayNotHasKey("_aConfVars", $aVars, "Internal var is wrongly returned");
    }

    /**
     * Tests that file is loaded only once
     */
    public function testFileIsLoadedOnlyOnce()
    {
        $oConfigFile = new OxConfigFile(getShopBasePath() . "config.inc.php");

        $sVar = $oConfigFile->getVar("sTsUser");
        $this->assertSame("oxid_esales", $sVar);

        //we should not use any public vars however we set it here as small workaround for our tests
        $oConfigFile->sTsUser = "test_value";
        $oConfigFile->setVar("sTsUser", 'test_value');

        //requesting once more
        $sVar = $oConfigFile->getVar("sTsUser");
        //new value should be returned, means the file was not parsed again
        $this->assertSame("test_value", $sVar);
    }

    /**
     * Tests that custom config is being set and variables from it are reachable
     *
     */
    public function testSetFile()
    {
        $sDir = getTestsBasePath() . '/unit/configtest';
        if (!is_dir($sDir)) {
            mkdir($sDir);
        }
        $sCustConfig = $sDir . "/cust_config.inc.php";
        $handle = fopen($sCustConfig, "w+");
        chmod($sCustConfig, 0777);
        $data = '<?php $this->custVar = test;';
        fwrite($handle, $data);

        $oConfigFile = new oxConfigFile(getShopBasePath() . "config.inc.php");
        $oConfigFile->setFile($sCustConfig);
        $sVar = $oConfigFile->getVar("custVar");

        $this->assertSame("test", $sVar);
    }
}
