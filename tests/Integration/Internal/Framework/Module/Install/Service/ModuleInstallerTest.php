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
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModuleInstallerTest extends TestCase
{
    use ContainerTrait;

    private string $moduleId = 'myTestModule';

    public function testUninstallNotActiveModule(): void
    {
        $package = $this->getOxidEshopPackage();
        $this->installModule();

        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->uninstall($package);

        $this->assertFalse(
            $moduleInstaller->isInstalled($package)
        );
    }

    public function testUninstallActiveModule(): void
    {
        $package = $this->getOxidEshopPackage();
        $this->installModule();
        $this->activateTestModule();

        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->uninstall($package);

        $this->assertFalse(
            $moduleInstaller->isInstalled($package)
        );
    }

    private function installModule(): void
    {
        $installService = $this->get(ModuleInstallerInterface::class);
        $package = $this->getOxidEshopPackage();
        $installService->install($package);
    }

    private function activateTestModule(): void
    {
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($this->moduleId, Registry::getConfig()->getShopId());
    }

    private function getOxidEshopPackage(): OxidEshopPackage
    {
        return new OxidEshopPackage(__DIR__ . '/Fixtures/' . $this->moduleId);
    }
}
