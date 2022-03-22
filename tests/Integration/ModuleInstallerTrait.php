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
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Container\ContainerInterface;

trait ModuleInstallerTrait
{
    public function addModules(array $moduleIds, string $modulesSource): void
    {
        foreach ($moduleIds as $moduleId) {
            $this->installModule($moduleId, "{$modulesSource}/{$moduleId}");
            $this->activateModule($moduleId);
        }
    }

    public function removeModules(array $moduleIds): void
    {
        foreach ($moduleIds as $moduleId) {
            $this->deactivateModule($moduleId);
            $this->removeModule($moduleId);
        }
    }

    public function installModule(string $moduleId, string $moduleSource): void
    {
        $package = new OxidEshopPackage($moduleId, $moduleSource);
        $package->setTargetDirectory($moduleId);
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

    public function removeModule(string $moduleId): void
    {
        $this->getContainer()->get('oxid_esales.symfony.file_system')
            ->remove(
                $this->getContainer()->get(ContextInterface::class)->getModulesPath() . $moduleId
            );
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
