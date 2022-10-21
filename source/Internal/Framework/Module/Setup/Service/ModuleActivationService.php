<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleActivationService implements ModuleActivationServiceInterface
{
    public function __construct(
        private ModuleConfigurationDaoInterface       $moduleConfigurationDao,
        private EventDispatcherInterface              $eventDispatcher,
        private ModuleConfigurationValidatorInterface $moduleConfigurationValidator,
        private ModuleServicesImporterInterface       $modulesYamlImportService,
        private ModulePathResolverInterface           $modulePathResolver,
    ) {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleConfigurationValidator->validate($moduleConfiguration, $shopId);

        $moduleConfiguration->setActivated(true);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->addModuleServices($moduleId, $shopId);

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

        $this->removeModuleServices($moduleId, $shopId);

        $moduleConfiguration->setActivated(false);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            new FinalizingModuleDeactivationEvent($shopId, $moduleId)
        );
    }

    private function addModuleServices(string $moduleId, int $shopId): void
    {
        try {
            $this->modulesYamlImportService->addImport($this->modulePathResolver->getFullModulePathFromConfiguration($moduleId, $shopId), $shopId);
            $this->eventDispatcher->dispatch(new ProjectYamlChangedEvent());
        } catch (NoServiceYamlException) {}
    }

    private function removeModuleServices(string $moduleId, int $shopId): void
    {
        $this->modulesYamlImportService->removeImport($this->modulePathResolver->getFullModulePathFromConfiguration($moduleId, $shopId), $shopId);
        $this->eventDispatcher->dispatch(new ProjectYamlChangedEvent());
    }
}
