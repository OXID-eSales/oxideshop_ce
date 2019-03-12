<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleSmartyPluginDirectoryTest
 */
class ModuleSmartyPluginDirectoriesTest extends TestCase
{
    use ContainerTrait;

    public function setUp()
    {
        $this->activateTestModule();
    }

    public function tearDown()
    {
        $this->deactivateTestModule();
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
        $this
            ->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(
                    'with_metadata_v21',
                    __DIR__ . '/Fixtures/with_metadata_v21',
                    []
                )
            );

        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('with_metadata_v21', Registry::getConfig()->getShopId());
    }

    private function deactivateTestModule()
    {
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate('with_metadata_v21', Registry::getConfig()->getShopId());
    }
}
