<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

use OxidEsales\EshopCommunity\Internal\Application\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
class TestContainerFactory
{
    public function create(): SymfonyContainerBuilder
    {
        $bootstrapContainer = BootstrapContainerFactory::getBootstrapContainer();
        $containerBuilder = new ContainerBuilder(
            $bootstrapContainer->get(BasicContextInterface::class)
        );

        $container = $containerBuilder->getContainer();
        $container = $this->setAllServicesAsPublic($container);
        $container = $this->setTestProjectConfigurationFile($container);

        return $container;
    }

    private function setAllServicesAsPublic(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        return $container;
    }

    private function setTestProjectConfigurationFile(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        $projectConfigurationYmlStorageDefinition = $container->getDefinition('oxid_esales.module.configuration.project_configuration_yaml_file_storage');
        $projectConfigurationYmlStorageDefinition->setArgument(
            '$filePath',
            tempnam(sys_get_temp_dir(), 'test_')
        );
        $container->setDefinition(
            'oxid_esales.module.configuration.project_configuration_yaml_file_storage',
            $projectConfigurationYmlStorageDefinition
        );

        return $container;
    }
}
