<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

class ModuleActivationBridge implements ModuleActivationBridgeInterface
{
    public function __construct(
        private ModuleActivationServiceInterface $moduleActivationService,
        private ModuleStateServiceInterface $moduleStateService
    ) {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->activate($moduleId, $shopId);
        Registry::getConfig()->reinitialize();
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->deactivate($moduleId, $shopId);
        Registry::getConfig()->reinitialize();
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return bool
     */
    public function isActive(string $moduleId, int $shopId): bool
    {
        return $this->moduleStateService->isActive($moduleId, $shopId);
    }
}
