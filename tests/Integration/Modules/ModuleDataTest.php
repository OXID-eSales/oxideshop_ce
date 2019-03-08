<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    public function testIncludeModuleMetaDataIncludeEmptyMetadata()
    {
        $this->markTestSkipped('Wont work. We use yml file instead.');

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
    public function testIncludeModuleMetaDataIncludeSetsModuleData()
    {
        $this->markTestSkipped('Wont work. We use yml file instead.');

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
    public function testIncludeModuleMetaDataIncludeSetsMetaDataVersion()
    {
        $this->markTestSkipped('Wont work. We use yml file instead.');

        $metaDataFile = $this->testModuleDirectory . 'metadata.php';

        $module = oxNew(Module::class);
        $module->includeModuleMetaData($metaDataFile);
        $metaDataVersion = $module->getMetaDataVersion();

        $this->assertSame('2.0', $metaDataVersion);
    }
}
