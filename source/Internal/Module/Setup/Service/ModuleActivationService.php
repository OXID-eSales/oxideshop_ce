<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\AfterModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ModuleSettingsHandlingServiceInterface
     */
    private $moduleSettingsHandlingService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $stateService;

    /**
     * @var ExtensionChainServiceInterface
     */
    private $classExtensionChainService;

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationDaoInterface        $ModuleConfigurationDao
     * @param EventDispatcherInterface               $eventDispatcher
     * @param ModuleSettingsHandlingServiceInterface $moduleSettingsHandlingService
     * @param ModuleStateServiceInterface            $stateService
     * @param ExtensionChainServiceInterface         $classExtensionChainService
     */
    public function __construct(
        ModuleConfigurationDaoInterface         $ModuleConfigurationDao,
        EventDispatcherInterface                $eventDispatcher,
        ModuleSettingsHandlingServiceInterface  $moduleSettingsHandlingService,
        ModuleStateServiceInterface             $stateService,
        ExtensionChainServiceInterface          $classExtensionChainService
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleSettingsHandlingService = $moduleSettingsHandlingService;
        $this->stateService = $stateService;
        $this->classExtensionChainService = $classExtensionChainService;
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        if ($this->stateService->isActive($moduleId, $shopId) === true) {
            throw new ModuleSetupException('Module with id "'. $moduleId . '" is already active.');
        }

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, $shopId);

        $this->stateService->setActive($moduleId, $shopId);

        $moduleConfiguration->setAutoActive(true);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->classExtensionChainService->updateChain($shopId);

        $this->eventDispatcher->dispatch(
            AfterModuleActivationEvent::NAME,
            new AfterModuleActivationEvent($shopId, $moduleId)
        );
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        if ($this->stateService->isActive($moduleId, $shopId) === false) {
            throw new ModuleSetupException('Module with id "'. $moduleId . '" is not active.');
        }

        $this->eventDispatcher->dispatch(
            BeforeModuleDeactivationEvent::NAME,
            new BeforeModuleDeactivationEvent($shopId, $moduleId)
        );

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnDeactivation($moduleConfiguration, $shopId);

        $this->stateService->setDeactivated($moduleId, $shopId);

        $moduleConfiguration->setAutoActive(false);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->classExtensionChainService->updateChain($shopId);
    }
}
