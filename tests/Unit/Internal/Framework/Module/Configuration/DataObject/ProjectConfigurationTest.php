<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use DomainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

class ProjectConfigurationTest extends TestCase
{
    /** @var ProjectConfiguration */
    private $projectConfiguration;

    protected function setUp()
    {
        parent::setUp();
        $this->projectConfiguration = new ProjectConfiguration();
    }

    public function testGetShopConfiguration()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(0, $shopConfiguration);
        $this->assertSame($shopConfiguration, $this->projectConfiguration->getShopConfiguration(0));
    }

    public function testGetShopConfigurations()
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


    public function testGetShopConfigurationThrowsExceptionWithNotExistingShopId()
    {
        $this->expectException(DomainException::class);
        $this->projectConfiguration->getShopConfiguration(0);
    }

    public function testGetShopIdsOfShopConfigurations()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(1, $shopConfiguration);
        $this->projectConfiguration->addShopConfiguration(2, $shopConfiguration);
        $this->assertEquals([1,2], $this->projectConfiguration->getShopConfigurationIds());
    }

    public function testDeleteShopConfiguration()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->projectConfiguration->addShopConfiguration(1, $shopConfiguration);
        $this->projectConfiguration->addShopConfiguration(2, $shopConfiguration);
        $this->projectConfiguration->deleteShopConfiguration(1);
        $this->assertEquals([2], $this->projectConfiguration->getShopConfigurationIds());
    }

    public function testDeleteShopConfigurationThrowsExceptionWithNotExistingShopId()
    {
        $this->expectException(DomainException::class);
        $this->projectConfiguration->deleteShopConfiguration(0);
    }
}
