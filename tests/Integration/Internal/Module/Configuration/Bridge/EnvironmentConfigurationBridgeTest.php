<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\EnvironmentConfigurationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class EnvironmentConfigurationBridgeTest extends TestCase
{
    use ContainerTrait;

    public function testSaving()
    {
        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1968, new ShopConfiguration());

        $environmentConfigurationBridge = $this->get(EnvironmentConfigurationBridgeInterface::class);

        $environmentConfigurationBridge->save($environmentConfiguration);

        $this->assertEquals(
            $environmentConfiguration,
            $environmentConfigurationBridge->get()
        );
    }
}
