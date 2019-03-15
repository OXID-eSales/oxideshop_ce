<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function setUp()
    {
        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
            ->generate();

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
            ->generate();

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
                'OxidEsales\Eshop\Application\Model\Article' => 'with_class_extensions/ModuleArticle',
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

    public function testGetPathsReturnsInstalledModulePahts()
    {
        $this->installModule('with_class_extensions');
        $this->installModule('with_metadata_v21');

        $module = oxNew(Module::class);


        $this->assertSame(
            [
                'with_class_extensions' => $this->getModuleConfiguration('with_class_extensions')->getPath(),
                'with_metadata_v21'     => $this->getModuleConfiguration('with_metadata_v21')->getPath(),
            ],
            $module->getModulePaths()
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

    private function getModuleConfiguration(string $moduleId)
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleConfigurationDaoBridgeInterface::class)
            ->get($moduleId);
    }
}
