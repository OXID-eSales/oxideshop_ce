<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModulesDataProvider implements ActiveModulesDataProviderInterface
{
    public function __construct(
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ModuleStateServiceInterface $moduleStateService,
        private ModulePathResolverInterface $modulePathResolver,
        private ContextInterface $context,
        private ModuleCacheServiceInterface $moduleCacheService
    ) {
    }

    /** @inheritDoc */
    public function getModuleIds(): array
    {
        $moduleIds = [];

        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $moduleIds[] = $moduleConfiguration->getId();
        }

        return $moduleIds;
    }

    /** @inheritDoc */
    public function getModulePaths(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'absolute_module_paths';

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $this->moduleCacheService->put(
                $cacheKey,
                $shopId,
                $this->collectModulePathsForCaching()
            );
        }
        return $this->moduleCacheService->get($cacheKey, $shopId);
    }

    /** @inheritDoc */
    public function getTemplates(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'templates';

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $this->moduleCacheService->put(
                $cacheKey,
                $shopId,
                $this->collectModuleTemplatesForCaching()
            );
        }
        return $this->createTemplatesFromData(
            $this->moduleCacheService->get($cacheKey, $shopId)
        );
    }

    /** @inheritDoc */
    public function getControllers(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'controllers';

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $this->moduleCacheService->put($cacheKey, $shopId, $this->collectControllersForCaching());
        }

        return $this->createControllersFromData(
            $this->moduleCacheService->get($cacheKey, $shopId)
        );
    }

    /** @return array */
    private function collectModulePathsForCaching(): array
    {
        $modulePaths = [];
        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $modulePaths[$moduleConfiguration->getId()] = $this
                ->modulePathResolver
                ->getFullModulePathFromConfiguration(
                    $moduleConfiguration->getId(),
                    $this->context->getCurrentShopId()
                );
        }
        return $modulePaths;
    }

    /** @return array */
    private function collectModuleTemplatesForCaching(): array
    {
        $templates = [];
        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            foreach ($moduleConfiguration->getTemplates() as $template) {
                $templates[$moduleConfiguration->getId()][$template->getTemplateKey()] = $template->getTemplatePath();
            }
        }
        return $templates;
    }

    /** @return array */
    private function collectControllersForCaching(): array
    {
        $controllers = [];
        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            foreach ($moduleConfiguration->getControllers() as $controller) {
                $controllers[$controller->getId()] = $controller->getControllerClassNameSpace();
            }
        }
        return $controllers;
    }

    /** @return ModuleConfiguration[] */
    private function getActiveModuleConfigurations(): array
    {
        $moduleConfigurations = [];
        $shopId = $this->context->getCurrentShopId();

        foreach ($this->shopConfigurationDao->get($shopId)->getModuleConfigurations() as $moduleConfiguration) {
            if ($this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)) {
                $moduleConfigurations[] = $moduleConfiguration;
            }
        }
        return $moduleConfigurations;
    }

    /**
     * @param array $data
     * @return Template[][]
     */
    private function createTemplatesFromData(array $data): array
    {
        $templates = [];
        foreach ($data as $moduleId => $templateData) {
            foreach ($templateData as $templateKey => $templatePath) {
                $templates[$moduleId][] = new Template($templateKey, $templatePath);
            }
        }
        return $templates;
    }

    /**
     * @param array $data
     * @return Template[][]
     */
    private function createControllersFromData(array $data): array
    {
        $controllers = [];
        foreach ($data as $id => $namespace) {
            $controllers[] = new ModuleConfiguration\Controller($id, $namespace);
        }

        return $controllers;
    }
}
