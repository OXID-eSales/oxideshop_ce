<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDataMapperTest extends TestCase
{
    public function testEnvironmentsMapping()
    {
        $configurationData = [
            'shops' => [],
        ];

        $projectConfiguration = new ProjectConfiguration();

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
            'shops' => [
                '1' => [],
                '2' => [],
            ],
        ];

        $projectConfiguration = new ProjectConfiguration();

        $projectConfiguration->addShopConfiguration(1, new ShopConfiguration());
        $projectConfiguration->addShopConfiguration(2, new ShopConfiguration());

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
