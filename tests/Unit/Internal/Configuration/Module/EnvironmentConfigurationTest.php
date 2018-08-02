<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module;

use OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdException;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

class EnvironmentConfigurationTest extends TestCase
{
    /** @var EnvironmentConfiguration */
    private $environmentConfiguration;

    protected function setUp()
    {
        parent::setUp();
        $this->environmentConfiguration = new EnvironmentConfiguration();
    }

    public function testGetShopConfiguration()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->environmentConfiguration->setShopConfiguration(0, $shopConfiguration);
        $this->assertSame($shopConfiguration, $this->environmentConfiguration->getShopConfiguration(0));
    }

    public function testGetShopConfigurationThrowsExceptionWithNotExistingShopId()
    {
        $this->expectException(InvalidObjectIdException::class);
        $this->environmentConfiguration->getShopConfiguration(0);
    }

    public function testGetShopIdsOfShopConfigurations()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->environmentConfiguration->setShopConfiguration(1, $shopConfiguration);
        $this->environmentConfiguration->setShopConfiguration(2, $shopConfiguration);
        $this->assertEquals([1,2], $this->environmentConfiguration->getShopIdsOfShopConfigurations());
    }

    public function testDeleteShopConfiguration()
    {
        $shopConfiguration = new ShopConfiguration();
        $this->environmentConfiguration->setShopConfiguration(1, $shopConfiguration);
        $this->environmentConfiguration->setShopConfiguration(2, $shopConfiguration);
        $this->environmentConfiguration->deleteShopConfiguration(1);
        $this->assertEquals([2], $this->environmentConfiguration->getShopIdsOfShopConfigurations());
    }

    public function testDeleteShopConfigurationThrowsExceptionWithNotExistingShopId()
    {
        $this->expectException(InvalidObjectIdException::class);
        $this->environmentConfiguration->deleteShopConfiguration(0);
    }

}