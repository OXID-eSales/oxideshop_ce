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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxConfigFile;

class OxConfigFileTest extends \OxidTestCase
{

    /**
     * Test for OxConfigFile::getVar() method
     */
    public function testGetVar()
    {
        $filePath = $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $oConfigFile = new oxConfigFile($filePath);

        $sVar = $oConfigFile->getVar("testVar");
        $this->assertSame("testValue", $sVar);
    }

    /**
     * Test for OxConfigFile::setVar() method
     */
    public function testSetVar()
    {
        $filePath = $this->createFile('config.inc.php', '<?php ');
        $oConfigFile = new oxConfigFile($filePath);

        $oConfigFile->setVar("testVar", 'testValue2');

        $sVar = $oConfigFile->getVar("testVar");
        $this->assertSame('testValue2', $sVar);
    }

    /**
     * Tests OxConfigFile::isVarSet() method
     */
    public function testIsVarSet()
    {
        $filePath = $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $oConfigFile = new oxConfigFile($filePath);

        $this->assertTrue($oConfigFile->isVarSet("testVar"), "Variable is supposed to be set");
        $this->assertFalse($oConfigFile->isVarSet("nonExistingVar"), "Variable is not supposed to be set");
    }

    /**
     * Test for OxConfigFile::getVars() method
     */
    public function testGetVars()
    {
        $filePath = $this->createFile('config.inc.php', '<?php $this->testVar = "testValue"; $this->testVar2 = "testValue2";');
        $oConfigFile = new oxConfigFile($filePath);

        $aVars = $oConfigFile->getVars();
        $expectedArray = array(
            'testVar' => 'testValue',
            'testVar2' => 'testValue2',
        );
        $this->assertSame($expectedArray, $aVars);
    }

    /**
     * Tests that file is loaded only once
     */
    public function testFileIsLoadedOnlyOnce()
    {
        $filePath = $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $oConfigFile = new oxConfigFile($filePath);

        $sVar = $oConfigFile->getVar("testVar");
        $this->assertSame("testValue", $sVar);

        $oConfigFile->setVar("testVar", 'testValue2');

        $this->assertSame("testValue2", $oConfigFile->getVar("testVar"));
    }

    /**
     * Tests that custom config is being set and variables from it are reachable
     */
    public function testSetFile()
    {
        $filePath = $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $oConfigFile = new oxConfigFile($filePath);

        $customConfigInc = $this->createFile('config.inc.php', '<?php $this->testVar2 = "testValue2";');
        $oConfigFile->setFile($customConfigInc);

        $this->assertSame("testValue2", $oConfigFile->getVar("testVar2"));
    }
}
