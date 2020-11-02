<?php

/**Utility
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveClassExtensionChainResolver implements ActiveClassExtensionChainResolverInterface
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * ActiveClassExtensionChainResolver constructor.
     */
    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleStateServiceInterface $moduleStateService,
        ContextInterface $context
    ) {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleStateService = $moduleStateService;
        $this->context = $context;
    }

    public function getActiveExtensionChain(int $shopId): ClassExtensionsChain
    {
        $shopConfiguration = $this->shopConfigurationDao->get($shopId);
        $classExtensionChain = $shopConfiguration->getClassExtensionsChain();

        $activeExtensions = [];

        foreach ($classExtensionChain as $shopClass => $moduleExtensionClasses) {
            $activeModuleExtensionClasses = $this->getActiveModuleExtensionClasses(
                $moduleExtensionClasses,
                $shopId,
                $shopConfiguration
            );

            if (!empty($activeModuleExtensionClasses)) {
                $activeExtensions[$shopClass] = $activeModuleExtensionClasses;
            }
        }

        $activeExtensionChain = new ClassExtensionsChain();
        $activeExtensionChain->setChain($activeExtensions);

        return $activeExtensionChain;
    }

    private function getActiveModuleExtensionClasses(
        array $moduleExtensionClasses,
        int $shopId,
        ShopConfiguration $shopConfiguration
    ): array {
        $activeClasses = [];

        foreach ($moduleExtensionClasses as $extensionClass) {
            if ($this->isActiveExtension($extensionClass, $shopId, $shopConfiguration)) {
                $activeClasses[] = $extensionClass;
            }
        }

        return $activeClasses;
    }

    private function isActiveExtension(
        string $classExtension,
        int $shopId,
        ShopConfiguration $shopConfiguration
    ): bool {
        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            if (
                $moduleConfiguration->hasClassExtension($classExtension)
                && $this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)
            ) {
                return true;
            }
        }

        return false;
    }
}
