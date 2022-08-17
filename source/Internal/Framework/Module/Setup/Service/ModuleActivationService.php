<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleActivationService implements ModuleActivationServiceInterface
{
    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private EventDispatcherInterface $eventDispatcher,
        private ModuleConfigurationHandlingServiceInterface $moduleConfigurationHandlingService,
        private ModuleServicesActivationServiceInterface $moduleServicesActivationService
    ) {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleConfigurationHandlingService->handleOnActivation($moduleConfiguration, $shopId);

        $this->moduleServicesActivationService->activateModuleServices($moduleId, $shopId);

        $moduleConfiguration->setActivated(true);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            new FinalizingModuleActivationEvent($shopId, $moduleId)
        );
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->eventDispatcher->dispatch(new BeforeModuleDeactivationEvent($shopId, $moduleId));

        $this->moduleConfigurationHandlingService->handleOnDeactivation($moduleConfiguration, $shopId);

        $this->moduleServicesActivationService->deactivateModuleServices($moduleId, $shopId);

        $moduleConfiguration->setActivated(false);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            new FinalizingModuleDeactivationEvent($shopId, $moduleId)
        );
    }
}
