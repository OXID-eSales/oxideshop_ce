<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDataMapperTest extends TestCase
{
    public function testEnvironmentsMapping()
    {
        $configurationData = [
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
        $projectConfiguration->addEnvironmentConfiguration('dev', new EnvironmentConfiguration());
        $projectConfiguration->addEnvironmentConfiguration('prod',new EnvironmentConfiguration());

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

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, new ShopConfiguration());
        $environmentConfiguration->addShopConfiguration(2, new ShopConfiguration());

        $projectConfiguration->addEnvironmentConfiguration(
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
