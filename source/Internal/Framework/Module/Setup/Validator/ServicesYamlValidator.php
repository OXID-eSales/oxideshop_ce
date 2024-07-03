<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidModuleServicesException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainer;
use Symfony\Component\Filesystem\Path;
use Throwable;

class ServicesYamlValidator implements ModuleConfigurationValidatorInterface
{
    private DIConfigWrapper $configFile;
    private DIConfigWrapper $originalConfigFile;
    private SymfonyContainer $fakeContainer;
    private string $moduleId;
    private int $shopId;

    public function __construct(
        private readonly ContextInterface $context,
        private readonly ProjectYamlDaoInterface $projectYamlDao,
        private readonly ModulePathResolverInterface $modulePathResolver
    ) {
    }

    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        $this->backupProjectConfigFile();
        $this->moduleId = $configuration->getId();
        $this->shopId = $shopId;
        try {
            $this->importValidatedModulesServicesIntoProjectConfigFile();
            $this->buildFakeContainerWithModifiedProjectConfigFile();
            $this->validateContainerDefinitions();
        } catch (NoServiceYamlException) {
            return;
        } catch (Throwable $e) {
            throw new InvalidModuleServicesException(
                message: "Service YAML for module [$this->moduleId] is invalid",
                previous: $e
            );
        } finally {
            $this->restoreProjectConfigFile();
        }
    }

    private function backupProjectConfigFile(): void
    {
        $this->configFile = $this->projectYamlDao->loadProjectConfigFile();
        $this->originalConfigFile = clone $this->configFile;
    }

    /**
     * We use project service file just to run validation, actual
     * module's service.yaml will be imported into active_module_services.yaml.
     * @throws NoServiceYamlException
     */
    private function importValidatedModulesServicesIntoProjectConfigFile(): void
    {
        $importFilePath = Path::join(
            $this->modulePathResolver->getFullModulePathFromConfiguration($this->moduleId, $this->shopId),
            'services.yaml'
        );
        if (!realpath($importFilePath)) {
            throw new NoServiceYamlException();
        }
        $this->configFile->addImport($importFilePath);
        $this->projectYamlDao->saveProjectConfigFile($this->configFile);
    }

    private function buildFakeContainerWithModifiedProjectConfigFile(): void
    {
        $this->fakeContainer = (new ContainerBuilder($this->context))->getContainer();
        foreach ($this->fakeContainer->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
        $this->fakeContainer->compile(true);
    }

    private function validateContainerDefinitions(): void
    {
        foreach ($this->fakeContainer->getDefinitions() as $definitionKey => $definition) {
            $this->fakeContainer->get($definitionKey);
        }
    }

    private function restoreProjectConfigFile(): void
    {
        $this->projectYamlDao->saveProjectConfigFile($this->originalConfigFile);
    }
}
