<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\AfterModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ModuleActivationService implements ModuleActivationServiceInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ModuleCacheServiceInterface
     */
    private $moduleCacheService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ModuleSettingsHandlingServiceInterface
     */
    private $moduleSettingsHandlingService;

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationDaoInterface        $ModuleConfigurationDao
     * @param EventDispatcherInterface               $eventDispatcher
     * @param ModuleCacheServiceInterface            $moduleCacheService
     * @param ModuleSettingsHandlingServiceInterface $moduleSettingsHandlingService
     */
    public function __construct(
        ModuleConfigurationDaoInterface         $ModuleConfigurationDao,
        EventDispatcherInterface                $eventDispatcher,
        ModuleCacheServiceInterface             $moduleCacheService,
        ModuleSettingsHandlingServiceInterface  $moduleSettingsHandlingService
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleCacheService = $moduleCacheService;
        $this->moduleSettingsHandlingService = $moduleSettingsHandlingService;
        //updateChain
        //handle module yml services / ShopActivationService
        // ACTIVE_MODULES: add to, delete from
        //autoActivate - projectConfigurationDao
        // State service
        // transaction service
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            AfterModuleActivationEvent::NAME,
            new AfterModuleActivationEvent($shopId, $moduleId)
        );

        $this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $this->eventDispatcher->dispatch(
            BeforeModuleDeactivationEvent::NAME,
            new BeforeModuleDeactivationEvent($shopId, $moduleId)
        );

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnDeactivation($moduleConfiguration, $shopId);

        $this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }
}
