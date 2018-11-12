<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\TestingLibrary\VfsStreamWrapper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    public function testProjectConfigurationSaving()
    {
        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('testProject');
        $projectConfiguration->setEnvironmentConfiguration('dev', new EnvironmentConfiguration());

        $projectConfigurationDao->persistConfiguration($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );
    }

    private function getContainer()
    {
        $containerBuilder = new ContainerBuilder();

        $container = $containerBuilder->getContainer();

        $yamlFileStorageDefinition = $container->getDefinition('oxid_esales.module.configuration.project_configuration_yaml_file_storage');
        $yamlFileStorageDefinition->setArgument('$filePath', $this->getTestConfigurationFilePath());

        $container->setDefinition(
            'oxid_esales.module.configuration.project_configuration_yaml_file_storage',
            $yamlFileStorageDefinition
        );

        $projectConfigurationDaoDefinition = $container->getDefinition(ProjectConfigurationDaoInterface::class);
        $projectConfigurationDaoDefinition->setPublic(true);

        $container->setDefinition(
            ProjectConfigurationDaoInterface::class,
            $projectConfigurationDaoDefinition
        );

        $container->compile();

        return $container;
    }

    /**
     * @return string
     */
    private function getTestConfigurationFilePath(): string
    {
        $vfsStreamWrapper = new VfsStreamWrapper();
        $relativePath = 'test/testProjectConfigurationDao.yaml';
        $path = $vfsStreamWrapper->getRootPath() . $relativePath;

        if (!is_file($path)) {
            $vfsStreamWrapper->createFile($relativePath);
        }

        return $path;
    }
}
