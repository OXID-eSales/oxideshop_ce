<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDataMapperTest extends TestCase
{
    public function testEnvironmentsMappingFromData()
    {
        $configurationData = [
            'project_name'  => 'Module structure 2018',
            'environments'  => [
                'dev' => [],
                'prod' => [],
            ],
        ];

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('Module structure 2018');

        $projectConfiguration->setEnvironmentConfiguration(
            'dev',
            new EnvironmentConfiguration()
        );

        $projectConfiguration->setEnvironmentConfiguration(
            'prod',
            new EnvironmentConfiguration()
        );

        $projectConfigurationDataMapper = new ProjectConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDataMapper->fromData($configurationData)
        );
    }

    public function testShopsMappingFromData()
    {
        $configurationData = [
            'project_name'  => 'Module structure 2018',
            'environments'  => [
                'dev' => [
                    'shops' => [
                        '1' => [],
                        '2' => [],
                    ],
                ],
            ],
        ];

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('Module structure 2018');

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->setShopConfiguration('1', new ShopConfiguration());
        $environmentConfiguration->setShopConfiguration('2', new ShopConfiguration());

        $projectConfiguration->setEnvironmentConfiguration(
            'dev',
            $environmentConfiguration
        );

        $projectConfigurationDataMapper = new ProjectConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDataMapper->fromData($configurationData)
        );
    }

    public function testModulesMappingFromData()
    {
        $configurationData = [
            'project_name'  => 'Module structure 2018',
            'environments'  => [
                'dev' => [
                    'shops' => [
                        '1' => [
                            'modules' => [
                                'happyModule' => [],
                                'funnyModule' => [],
                            ],
                            'moduleChains' => [

                            ],
                        ],
                    ],
                ],
            ],
        ];

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('Module structure 2018');

        $shopConfigurationWithModules = new ShopConfiguration();
        $shopConfigurationWithModules->setModuleConfiguration('happyModule', new ModuleConfiguration());
        $shopConfigurationWithModules->setModuleConfiguration('funnyModule', new ModuleConfiguration());

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->setShopConfiguration('1', $shopConfigurationWithModules);

        $projectConfiguration->setEnvironmentConfiguration(
            'dev',
            $environmentConfiguration
        );

        $projectConfigurationDataMapper = new ProjectConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDataMapper->fromData($configurationData)
        );
    }

    private function getModuleConfigurationMapper()
    {
        $moduleConfigurationDataMapper = $this
            ->getMockBuilder(ModuleConfigurationDataMapperInterface::class)
            ->getMock();

        $moduleConfigurationDataMapper
            ->method('fromData')
            ->willReturn(new ModuleConfiguration());

        return $moduleConfigurationDataMapper;
    }
}
