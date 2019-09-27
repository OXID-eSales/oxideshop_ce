<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model\Module;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Core\Module\ModuleExtensionsCleaner;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleExtensionsCleanerTest extends UnitTestCase
{
    /**
     * Test case for bug #6342
     */
    private $testModuleId = 'with_class_extensions_cleaner';

    public function tearDown()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $fileSystem = $container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($container->get(ContextInterface::class)->getModulesPath() . '/' . $this->testModuleId);
        parent::tearDown();
    }

    public function testChecksIfModuleIdDoesNotDependOnDirectory()
    {
        $this->installTestModule();

        $installedExtensions = [
            Article::class => ['with_class_extensions_cleaner/ModuleArticle'],
            'otherEshopClass' => ['with_class_extensions_cleaner/testModuleDirectory/class/which/is/garbage'],
            'yetAnotherEshopClass' => ['anyModule/testModuleDirectory/class/which/is/not/garbage'],
        ];

        $cleanedExtensionsData = [
            Article::class => ['with_class_extensions_cleaner/ModuleArticle'],
            'yetAnotherEshopClass' => ['anyModule/testModuleDirectory/class/which/is/not/garbage'],
        ];

        /** @var ModuleExtensionsCleaner $extensionsCleaner */
        $extensionsCleaner = oxNew(ModuleExtensionsCleaner::class);
        $module = oxNew(Module::class);
        $module->load($this->testModuleId);

        $this->assertSame($cleanedExtensionsData, $extensionsCleaner->cleanExtensions($installedExtensions, $module));
    }

    private function installTestModule()
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $container
            ->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(
                    $this->testModuleId,
                    __DIR__ . '/Fixtures/' . $this->testModuleId
                )
            );
    }
}
