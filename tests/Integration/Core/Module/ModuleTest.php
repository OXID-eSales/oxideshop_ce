<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    use ContainerTrait;

    public function tearDown()
    {
        parent::tearDown();

        Registry::getConfig()->saveShopConfVar('aarr', 'activeModules', []);
    }

    public function testIsActiveIfModuleIsActive()
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertTrue($module->isActive());
    }

    public function testIsActiveIfModuleIsNotActive()
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertFalse($module->isActive());
    }

    public function testHasExtendClassReturnsTrue()
    {
        $moduleId = 'with_class_extensions';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertTrue($module->hasExtendClass());
    }

    public function testHasExtendClassReturnsFalse()
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertFalse($module->hasExtendClass());
    }

    public function testGetExtensions()
    {
        $moduleId = 'with_class_extensions';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertSame(
            [
                'OxidEsales\Eshop\Application\Model\Article' => 'ModuleArticle',
            ],
            $module->getExtensions()
        );
    }

    public function testGetExtensionsReturnsEmptyArrayIfNoExtensions()
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertSame(
            [],
            $module->getExtensions()
        );
    }

    private function installModule(string $id)
    {
        ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(
                    $id,
                    __DIR__ . '/Fixtures/' . $id,
                    []
                )
            );
    }

    private function activateModule(string $id)
    {
        ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($id, 1);
    }
}
