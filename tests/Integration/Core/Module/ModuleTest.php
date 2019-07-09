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
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
             ->generate();

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->removeTestModules();

        $this->container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
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

    public function testModuleGetTemplates()
    {
        $moduleId = "with_extending_blocks";

        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $expected = [
            'page/checkout/basket.tpl',
            'page/checkout/payment.tpl',
            'page/checkout/basket.tpl'
        ];

        $actual = $module->getTemplates();

        $this->assertEquals(0, count(array_diff($expected, $actual)) + count(array_diff($actual, $expected)));
    }

    public function testGetPathsReturnsInstalledModulePaths()
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
        $package = new OxidEshopPackage($id, __DIR__ . '/Fixtures/' . $id);
        $package->setTargetDirectory('oeTest/' . $id);

        $this->container->get(ModuleInstallerInterface::class)
            ->install($package);
    }

    private function activateModule(string $id)
    {
        $this->container->get(ModuleActivationBridgeInterface::class)
            ->activate($id, 1);
    }

    private function getModuleConfiguration(string $moduleId)
    {
        return $this->container->get(ModuleConfigurationDaoBridgeInterface::class)
            ->get($moduleId);
    }

    private function removeTestModules()
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->container->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
    }

    public function testHasMetadataReturnsTrue()
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertTrue($module->hasMetadata());
    }

    public function testGetModuleIdByClassName()
    {
        $moduleId = 'with_class_extensions';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $this->assertEquals(
            'with_class_extensions',
            oxNew(Module::class)->getModuleIdByClassName("with_class_extensions/ModuleArticle")
        );
    }
}
