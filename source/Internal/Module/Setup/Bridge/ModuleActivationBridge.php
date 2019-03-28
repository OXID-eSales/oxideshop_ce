<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;

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
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * ModuleActivationBridge constructor.
     * @param ModuleActivationServiceInterface $moduleActivationService
     * @param ModuleStateServiceInterface      $moduleStateService
     */
    public function __construct(
        ModuleActivationServiceInterface    $moduleActivationService,
        ModuleStateServiceInterface         $moduleStateService
    ) {
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleStateService = $moduleStateService;
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
