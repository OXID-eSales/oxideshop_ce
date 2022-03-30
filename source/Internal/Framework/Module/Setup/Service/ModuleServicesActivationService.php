<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ServicesYamlConfigurationErrorEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidModuleServicesException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\PathUtil\Path;

class ModuleServicesActivationService implements ModuleServicesActivationServiceInterface
{
    /**
     * ModuleServicesActivationService constructor.
     */
    public function __construct(private ProjectYamlDaoInterface $dao, public EventDispatcherInterface $eventDispatcher, private ModulePathResolverInterface $modulePathResolver, private ModuleStateServiceInterface $moduleStateService, private ContextInterface $context)
    {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\MissingServiceException
     */
    public function activateModuleServices(string $moduleId, int $shopId)
    {
        $moduleConfigFile = $this->getModuleServicesFilePath($moduleId, $shopId);
        try {
            $moduleConfig = $this->getModuleConfig($moduleConfigFile);
        } catch (NoServiceYamlException) {
            return;
        }

        $projectConfig = $this->dao->loadProjectConfigFile();
        $projectConfig->addImport($this->getRelativeModuleConfigFilePath($moduleConfigFile));

        /** @var DIServiceWrapper $service */
        foreach ($moduleConfig->getServices() as $service) {
            if (!$service->isShopAware()) {
                continue;
            }
            if ($projectConfig->hasService($service->getKey())) {
                $service = $projectConfig->getService($service->getKey());
            }
            $service->addActiveShops([$shopId]);
            $projectConfig->addOrUpdateService($service);
        }

        $this->dao->saveProjectConfigFile($projectConfig);
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\MissingServiceException
     */
    public function deactivateModuleServices(string $moduleId, int $shopId)
    {
        $moduleConfigFile = $this->getModuleServicesFilePath($moduleId, $shopId);
        try {
            $moduleConfig = $this->getModuleConfig($moduleConfigFile);
        } catch (NoServiceYamlException) {
            return;
        } catch (InvalidModuleServicesException) {
            // it could never have been activated so there is nothing to deactivate
            // and we can safely ignore this deactivation request
            return;
        }

        $projectConfig = $this->dao->loadProjectConfigFile();

        $this->cleanupShopAwareServices($projectConfig, $moduleConfig, $shopId);

        if ($this->isLastActiveShop($moduleId, $shopId)) {
            $projectConfig->removeImport($this->getRelativeModuleConfigFilePath($moduleConfigFile));
        }

        $this->dao->saveProjectConfigFile($projectConfig);
    }

    private function isLastActiveShop(string $moduleId, int $currentShopId): bool
    {
        foreach ($this->context->getAllShopIds() as $shopId) {
            if ($shopId === $currentShopId) {
                continue;
            }
            if ($this->moduleStateService->isActive($moduleId, $shopId)) {
                return false;
            }
        }
        return true;
    }

    private function cleanupShopAwareServices(
        DIConfigWrapper $projectConfig,
        DIConfigWrapper $moduleConfig,
        int $shopId
    ) {
        foreach ($moduleConfig->getServices() as $service) {
            if ($service->isShopAware() && $projectConfig->hasService($service->getKey())) {
                $service = $projectConfig->getService($service->getKey());
                $service->removeActiveShops([$shopId]);
                $projectConfig->addOrUpdateService($service);
            }
        }
    }

    /**
     * @param string $moduleConfigFile
     *
     * @return DIConfigWrapper
     * @throws NoServiceYamlException
     * @throws InvalidModuleServicesException
     */
    private function getModuleConfig(string $moduleConfigFile): DIConfigWrapper
    {
        if (!file_exists($moduleConfigFile)) {
            throw new NoServiceYamlException();
        }

        $moduleConfig = $this->dao->loadDIConfigFile($moduleConfigFile);
        if (!$moduleConfig->checkServiceClassesCanBeLoaded()) {
            $this->eventDispatcher->dispatch(
                new ServicesYamlConfigurationErrorEvent(
                    'Service class can not be loaded',
                    $moduleConfigFile
                ),
                ServicesYamlConfigurationErrorEvent::NAME
            );
            throw new InvalidModuleServicesException();
        }

        return $moduleConfig;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return string
     */
    private function getModuleServicesFilePath(string $moduleId, int $shopId): string
    {
        return $this->modulePathResolver->getFullModulePathFromConfiguration($moduleId, $shopId)
            . DIRECTORY_SEPARATOR . 'services.yaml';
    }

    /**
     * @param string $moduleConfigFile
     * @return string
     */
    private function getRelativeModuleConfigFilePath(string $moduleConfigFile): string
    {
        return Path::makeRelative(
            $moduleConfigFile,
            Path::getDirectory($this->context->getGeneratedServicesFilePath())
        );
    }
}
