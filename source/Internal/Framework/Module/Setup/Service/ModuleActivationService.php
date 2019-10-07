<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var ModuleConfigurationHandlingServiceInterface
     */
    private $moduleConfigurationHandlingService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $stateService;

    /**
     * @var ExtensionChainServiceInterface
     */
    private $classExtensionChainService;

    /**
     * @var ModuleServicesActivationServiceInterface
     */
    private $moduleServicesActivationService;

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationDaoInterface             $moduleConfigurationDao
     * @param EventDispatcherInterface                    $eventDispatcher
     * @param ModuleConfigurationHandlingServiceInterface $moduleSettingsHandlingService
     * @param ModuleStateServiceInterface                 $stateService
     * @param ExtensionChainServiceInterface              $classExtensionChainService
     * @param ModuleServicesActivationServiceInterface    $moduleServicesActivationService
     */
    public function __construct(
        ModuleConfigurationDaoInterface             $moduleConfigurationDao,
        EventDispatcherInterface                    $eventDispatcher,
        ModuleConfigurationHandlingServiceInterface $moduleSettingsHandlingService,
        ModuleStateServiceInterface                 $stateService,
        ExtensionChainServiceInterface              $classExtensionChainService,
        ModuleServicesActivationServiceInterface    $moduleServicesActivationService
    ) {
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleConfigurationHandlingService = $moduleSettingsHandlingService;
        $this->stateService = $stateService;
        $this->classExtensionChainService = $classExtensionChainService;
        $this->moduleServicesActivationService = $moduleServicesActivationService;
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleSetupException
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateIsAlreadySetException
     */
    public function activate(string $moduleId, int $shopId)
    {
        if ($this->stateService->isActive($moduleId, $shopId) === true) {
            throw new ModuleSetupException('Module with id "'. $moduleId . '" is already active.');
        }

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleConfigurationHandlingService->handleOnActivation($moduleConfiguration, $shopId);

        $this->moduleServicesActivationService->activateModuleServices($moduleId, $shopId);

        $this->stateService->setActive($moduleId, $shopId);

        $moduleConfiguration->setConfigured(true);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->classExtensionChainService->updateChain($shopId);

        $this->eventDispatcher->dispatch(
            FinalizingModuleActivationEvent::NAME,
            new FinalizingModuleActivationEvent($shopId, $moduleId)
        );
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleSetupException
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateIsAlreadySetException
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

        $this->moduleConfigurationHandlingService->handleOnDeactivation($moduleConfiguration, $shopId);

        $this->moduleServicesActivationService->deactivateModuleServices($moduleId, $shopId);

        $this->stateService->setDeactivated($moduleId, $shopId);

        $moduleConfiguration->setConfigured(false);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->classExtensionChainService->updateChain($shopId);

        $this->eventDispatcher->dispatch(
            FinalizingModuleDeactivationEvent::NAME,
            new FinalizingModuleDeactivationEvent($shopId, $moduleId)
        );
    }
}
