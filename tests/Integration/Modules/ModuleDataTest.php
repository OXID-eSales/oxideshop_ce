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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\BaseModuleTestCase;

class ModuleDataTest extends BaseModuleTestCase
{
    protected $testModuleDirectory;

    public function setUp()
    {
        parent::setUp();

        $this->testModuleDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'module_data_test' . DIRECTORY_SEPARATOR;
    }

    /**
     * Test, that including a metadata file without proper metadata does not break anything
     *
     * @covers OxidEsales\Eshop\Core\Module\Module::includeModuleMetaData()
     */
    public function testIncludeModuleMetaDataIncludeEmptyMetadata() {
        $module = oxNew(Module::class);

        $metaDataFile = $this->testModuleDirectory . 'emptyMetaData.php';

        $module->includeModuleMetaData($metaDataFile);

        $aModules = $module->getModuleData();
        $metaDataVersion = $module->getMetaDataVersion();

        $this->assertEmpty($aModules, 'Module::includeModuleMetaData() creates an empty array for ModulData, if metadata.php is empty');
        $this->assertTrue(!isset($metaDataVersion), 'Module::includeModuleMetaData() leaves metaDataVersion unset, if metadata.php is empty');
    }

    /**
     * Test, that including a metadata file returns the expected value for Module::getModuleData()
     *
     * @covers OxidEsales\Eshop\Core\Module\Module::includeModuleMetaData()
     */
    public function testIncludeModuleMetaDataIncludeSetsModuleData() {
        $metaDataFile = $this->testModuleDirectory . 'metadata.php';

        $module = oxNew(Module::class);
        $module->includeModuleMetaData($metaDataFile);
        $aModules = $module->getModuleData();

        $this->assertSame(['somekey' => 'somevalue'], $aModules, 'Module::includeModuleMetaData() populates Module::_aModule with the correct values');
    }

    /**
     * Test, that including a metadata file returns the expected value for Module::getMetaDataVersion()
     *
     * @covers OxidEsales\Eshop\Core\Module\Module::includeModuleMetaData()
     */
    public function testIncludeModuleMetaDataIncludeSetsMetaDataVersion() {
        $metaDataFile = $this->testModuleDirectory . 'metadata.php';

        $module = oxNew(Module::class);
        $module->includeModuleMetaData($metaDataFile);
        $metaDataVersion = $module->getMetaDataVersion();

        $this->assertSame('2.0', $metaDataVersion);
    }
}
