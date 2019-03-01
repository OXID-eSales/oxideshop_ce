<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;

/**
 * @internal
 */
class ModuleActivationBridge implements ModuleActivationBridgeInterface
{
    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * ModuleActivationBridge constructor.
     * @param ModuleActivationServiceInterface $moduleActivationService
     */
    public function __construct(ModuleActivationServiceInterface $moduleActivationService)
    {
        $this->moduleActivationService = $moduleActivationService;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->activate($moduleId, $shopId);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->deactivate($moduleId, $shopId);
    }
}
