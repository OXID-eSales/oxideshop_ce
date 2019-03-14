<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge;

use OxidEsales\Eshop\Core\Config;
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
     * @var Config
     */
    private $config;

    /**
     * ModuleActivationBridge constructor.
     * @param ModuleActivationServiceInterface $moduleActivationService
     * @param ModuleStateServiceInterface      $moduleStateService
     * @param Config                           $config
     */
    public function __construct(
        ModuleActivationServiceInterface    $moduleActivationService,
        ModuleStateServiceInterface         $moduleStateService,
        Config                              $config
    ) {
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleStateService = $moduleStateService;
        $this->config = $config;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->activate($moduleId, $shopId);
        $this->config->reinitialize();
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $this->moduleActivationService->deactivate($moduleId, $shopId);
        $this->config->reinitialize();
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
