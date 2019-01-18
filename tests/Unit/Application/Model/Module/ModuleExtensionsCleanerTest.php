<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model\Module;

use OxidEsales\EshopCommunity\Core\Module\ModuleExtensionsCleaner;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleExtensionsCleanerTest extends UnitTestCase
{
    /**
     * Test case for bug #6342
     */
    public function testChecksIfModuleIdDoesNotDependOnDirectory()
    {
        $installedExtensions = [
            'eShopClass' => ['vendorName/testModuleDirectory/moduleClass'],
            'otherEshopClass' => ['vendorName/testModuleDirectory/class/which/is/garbage'],
        ];
        $moduleData = [
            'id' => 'testModuleId',
            'extend' => [
                'eShopClass' => 'vendorName/testModuleDirectory/moduleClass',
            ]
        ];
        $cleanedExtensionsData = [
            'eShopClass' => ['vendorName/testModuleDirectory/moduleClass'],
        ];
        $modulePaths = ['testModuleId' => 'vendorName/testModuleDirectory'];

        \oxRegistry::getConfig()->setConfigParam('aModulePaths', $modulePaths);
        /** @var ModuleExtensionsCleaner $extensionsCleaner */
        $extensionsCleaner = oxNew(ModuleExtensionsCleaner::class);
        $module = oxNew('oxModule');
        $module->setModuleData($moduleData);

        $this->assertSame($cleanedExtensionsData, $extensionsCleaner->cleanExtensions($installedExtensions, $module));
    }
}
