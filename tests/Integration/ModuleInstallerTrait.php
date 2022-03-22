<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Psr\Container\ContainerInterface;

trait ModuleInstallerTrait
{
    /**
     * @param array<string, string> $modulesConfiguration
     * @return void
     */
    public function addModules(array $modulesConfiguration): void
    {
        foreach ($modulesConfiguration as $moduleId => $packagePath) {
            $this->installModule($packagePath);
            $this->activateModule($moduleId);
        }
    }

    /**
     * @param array<string, string> $modulesConfiguration
     * @return void
     */
    public function removeModules(array $modulesConfiguration): void
    {
        foreach ($modulesConfiguration as $moduleId => $packagePath) {
            $this->deactivateModule($moduleId);
            $this->removeModule($packagePath);
        }
    }

    public function installModule(string $packagePath): void
    {
        $package = new OxidEshopPackage($packagePath);
        $this->getContainer()->get(ModuleInstallerInterface::class)->install($package);
    }

    public function activateModule(string $moduleId): void
    {
        $this->getContainer()->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, Registry::getConfig()->getShopId());
    }

    public function deactivateModule(string $moduleId): void
    {
        $this->getContainer()
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, Registry::getConfig()->getShopId());
    }

    public function removeModule(string $packagePath): void
    {
        $this->getContainer()->get('oxid_esales.symfony.file_system')
            ->remove(
                $packagePath
            );
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
