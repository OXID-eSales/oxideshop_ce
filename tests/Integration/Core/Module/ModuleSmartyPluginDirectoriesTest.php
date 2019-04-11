<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleSmartyPluginDirectoryTest
 */
class ModuleSmartyPluginDirectoriesTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
            ->generate();

        $this->activateTestModule();
    }

    public function tearDown()
    {
        $this->deactivateTestModule();

        $this->removeTestModules();

        $this->container
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
            ->generate();
    }

    /**
     * Smarty should know about the smarty plugin directories of the modules being activated.
     */
    public function testModuleSmartyPluginDirectoryIsIncludedOnModuleActivation()
    {
        $utilsView = oxNew(UtilsView::class);
        $smarty = $utilsView->getSmarty(true);

        $this->assertTrue(
            $this->isPathInSmartyDirectories($smarty, 'Smarty/PluginDirectory1WithMetadataVersion21')
        );

        $this->assertTrue(
            $this->isPathInSmartyDirectories($smarty, 'Smarty/PluginDirectory2WithMetadataVersion21')
        );
    }

    public function testSmartyPluginDirectoriesOrder()
    {
        $utilsView = oxNew(UtilsView::class);
        $smarty = $utilsView->getSmarty(true);

        $this->assertModuleSmartyPluginDirectoriesFirst($smarty->plugins_dir);
        $this->assertShopSmartyPluginDirectorySecond($smarty->plugins_dir);
    }

    private function assertModuleSmartyPluginDirectoriesFirst($directories)
    {
        $this->assertContains(
            'Smarty/PluginDirectory1WithMetadataVersion21',
            $directories[0]
        );

        $this->assertContains(
            'Smarty/PluginDirectory2WithMetadataVersion21',
            $directories[1]
        );
    }

    private function assertShopSmartyPluginDirectorySecond($directories)
    {
        $this->assertContains(
            'Core/Smarty/Plugin',
            $directories[2]
        );
    }

    private function isPathInSmartyDirectories($smarty, $path)
    {
        foreach ($smarty->plugins_dir as $directory) {
            if (strpos($directory, $path)) {
                return true;
            }
        }

        return false;
    }

    private function activateTestModule()
    {
        $id = 'with_metadata_v21';
        $package = new OxidEshopPackage($id, __DIR__ . '/Fixtures/' . $id);
        $package->setTargetDirectory('oeTest/' . $id);

        $this->container->get(ModuleInstallerInterface::class)
            ->install($package);

        $this->container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('with_metadata_v21', Registry::getConfig()->getShopId());
    }

    private function deactivateTestModule()
    {
        $this->container
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate('with_metadata_v21', Registry::getConfig()->getShopId());
    }

    private function removeTestModules()
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->container->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
    }
}
