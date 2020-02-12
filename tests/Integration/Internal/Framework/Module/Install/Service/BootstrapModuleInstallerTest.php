<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Install\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class BootstrapModuleInstallerTest extends TestCase
{
    use ContainerTrait;
    
    private $moduleId = 'myTestModule';

    public function testUninstall(): void
    {
        $package = new OxidEshopPackage($this->moduleId, __DIR__ . '/Fixtures/' . $this->moduleId);

        $this->installModule($package);
        $this->activateTestModule($package);

        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->uninstall($package);

        $this->assertFalse(
            $moduleInstaller->isInstalled($package)
        );
    }

    /**
     * @param OxidEshopPackage $package
     */
    private function installModule(OxidEshopPackage $package): void
    {
        $installService = $this->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($this->moduleId, __DIR__ . '/Fixtures/' . $this->moduleId);
        $package->setTargetDirectory('oeTest/' . $this->moduleId);
        $installService->install($package);
    }

    /**
     * @param OxidEshopPackage $package
     */
    private function activateTestModule(OxidEshopPackage $package): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($package);
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($this->moduleId, Registry::getConfig()->getShopId());
    }
}
