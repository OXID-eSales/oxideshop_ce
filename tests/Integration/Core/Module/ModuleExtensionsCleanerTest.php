<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model\Module;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Module\ModuleExtensionsCleaner;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleExtensionsCleanerTest extends UnitTestCase
{
    /**
     * Test case for bug #6342
     */
    public function testChecksIfModuleIdDoesNotDependOnDirectory()
    {
        $this->activateTestModule();

        $installedExtensions = [
            Article::class => ['with_class_extensions/ModuleArticle'],
            'otherEshopClass' => ['with_class_extensions/testModuleDirectory/class/which/is/garbage'],
        ];

        $cleanedExtensionsData = [
            Article::class => ['with_class_extensions/ModuleArticle'],
        ];

        /** @var ModuleExtensionsCleaner $extensionsCleaner */
        $extensionsCleaner = oxNew(ModuleExtensionsCleaner::class);
        $module = oxNew('oxModule');
        $module->load('with_class_extensions');

        $this->assertSame($cleanedExtensionsData, $extensionsCleaner->cleanExtensions($installedExtensions, $module));
    }

    private function activateTestModule()
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $container
            ->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(
                    'with_class_extensions',
                    __DIR__ . '/Fixtures/with_class_extensions',
                    []
                )
            );

        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('with_class_extensions', Registry::getConfig()->getShopId());
    }
}
