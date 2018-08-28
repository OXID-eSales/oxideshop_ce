<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDataMapperTest extends TestCase
{
    public function testEnvironmentsMapping()
    {
        $configurationData = [
            'project_name'  => 'Module structure 2018',
            'environments'  => [
                'dev' => [
                    'shops' => [],
                ],
                'prod' => [
                    'shops' => [],
                ],
            ],
        ];

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('Module structure 2018');

        $projectConfiguration->setEnvironmentConfiguration('dev', new EnvironmentConfiguration());
        $projectConfiguration->setEnvironmentConfiguration('prod',new EnvironmentConfiguration());

        $projectConfigurationDataMapper = new ProjectConfigurationDataMapper(
            $this->getMockBuilder(ShopConfigurationDataMapperInterface::class)->getMock()
        );

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDataMapper->fromData($configurationData)
        );

        $this->assertEquals(
            $configurationData,
            $projectConfigurationDataMapper->toData($projectConfiguration)
        );
    }

    public function testShopsMapping()
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
        $environmentConfiguration->setShopConfiguration(1, new ShopConfiguration());
        $environmentConfiguration->setShopConfiguration(2, new ShopConfiguration());

        $projectConfiguration->setEnvironmentConfiguration(
            'dev',
            $environmentConfiguration
        );

        $shopConfigurationDataMapper = $this
            ->getMockBuilder(ShopConfigurationDataMapperInterface::class)
            ->getMock();

        $shopConfigurationDataMapper
            ->method('fromData')
            ->with($this->equalTo([]))
            ->willReturn(new ShopConfiguration());

        $projectConfigurationDataMapper = new ProjectConfigurationDataMapper($shopConfigurationDataMapper);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDataMapper->fromData($configurationData)
        );

        $this->assertEquals(
            $configurationData,
            $projectConfigurationDataMapper->toData($projectConfiguration)
        );
    }
}
