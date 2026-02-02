<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidModuleServicesException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use Symfony\Component\Filesystem\Path;

class ServicesYamlValidator implements ModuleConfigurationValidatorInterface
{
    public function __construct(
        private BasicContextInterface $basicContext,
        private ProjectYamlDaoInterface $projectYamlDao,
        private ModulePathResolverInterface $modulePathResolver
    ) {
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int $shopId
     * @throws \Throwable
     */
    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        $projectYaml = $this->projectYamlDao->loadProjectConfigFile();
        $originalProjectYaml = clone $projectYaml;

        try {
            $projectYaml->addImport(
                Path::join(
                    $this->modulePathResolver->getFullModulePathFromConfiguration($configuration->getId(), $shopId),
                    'services.yaml'
                )
            );
            $this->projectYamlDao->saveProjectConfigFile($projectYaml);

            $container = $this->buildContainer();
            $this->checkContainer($container);
        } catch (NoServiceYamlException $e) {
            return;
        } catch (\Throwable $e) {
            throw new InvalidModuleServicesException(
                "Service Yaml for moduleId of [" . $configuration->getId() . "] is invalid",
                0,
                $e
            );
        } finally {
            // Restore the old settings
            $this->projectYamlDao->saveProjectConfigFile($originalProjectYaml);
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     * @throws \Exception
     */
    private function buildContainer(): \Symfony\Component\DependencyInjection\ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder($this->basicContext);
        $container = $containerBuilder->getContainer();
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
        $container->compile(true);
        return $container;
    }

    /**
     * Try to fetch all services defined
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Exception
     */
    private function checkContainer(\Symfony\Component\DependencyInjection\ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definitionKey => $definition) {
            if ($definition->isPublic()) {
                $container->get($definitionKey);
            }
        }
    }
}
