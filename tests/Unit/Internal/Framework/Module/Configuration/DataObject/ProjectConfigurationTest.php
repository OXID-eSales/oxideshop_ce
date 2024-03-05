<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use DomainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

final class ProjectConfigurationTest extends TestCase
{
    private ProjectConfiguration $projectConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectConfiguration = new ProjectConfiguration();
    }

    public function testGetShopConfiguration(): void
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(0, $shopConfiguration);
        $this->assertSame($shopConfiguration, $this->projectConfiguration->getShopConfiguration(0));
    }

    public function testGetShopConfigurations(): void
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(0, $shopConfiguration);
        $this->projectConfiguration->addShopConfiguration(1, $shopConfiguration);

        $this->assertSame(
            [
                0 => $shopConfiguration,
                1 => $shopConfiguration,
            ],
            $this->projectConfiguration->getShopConfigurations()
        );
    }


    public function testGetShopConfigurationThrowsExceptionWithNotExistingShopId(): void
    {
        $this->expectException(DomainException::class);
        $this->projectConfiguration->getShopConfiguration(0);
    }

    public function testGetShopIdsOfShopConfigurations(): void
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(1, $shopConfiguration);
        $this->projectConfiguration->addShopConfiguration(2, $shopConfiguration);
        $this->assertEquals([1,2], $this->projectConfiguration->getShopConfigurationIds());
    }

    public function testDeleteShopConfiguration(): void
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(1, $shopConfiguration);
        $this->projectConfiguration->addShopConfiguration(2, $shopConfiguration);
        $this->projectConfiguration->deleteShopConfiguration(1);
        $this->assertEquals([2], $this->projectConfiguration->getShopConfigurationIds());
    }

    public function testDeleteShopConfigurationThrowsExceptionWithNotExistingShopId(): void
    {
        $this->expectException(DomainException::class);
        $this->projectConfiguration->deleteShopConfiguration(0);
    }
}
