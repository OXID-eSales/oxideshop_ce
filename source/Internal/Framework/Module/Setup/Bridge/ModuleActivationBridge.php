<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

class ModuleActivationBridge implements ModuleActivationBridgeInterface
{
    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * ModuleActivationBridge constructor.
     */
    public function __construct(
        ModuleActivationServiceInterface $moduleActivationService,
        ModuleStateServiceInterface $moduleStateService
    ) {
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleStateService = $moduleStateService;
    }

    /**
     * @throws ModuleSetupException
     */
    public function activate(string $moduleId, int $shopId): void
    {
        $this->moduleActivationService->activate($moduleId, $shopId);
        Registry::getConfig()->reinitialize();
    }

    /**
     * @throws ModuleSetupException
     */
    public function deactivate(string $moduleId, int $shopId): void
    {
        $this->moduleActivationService->deactivate($moduleId, $shopId);
        Registry::getConfig()->reinitialize();
    }

    public function isActive(string $moduleId, int $shopId): bool
    {
        return $this->moduleStateService->isActive($moduleId, $shopId);
    }
}
