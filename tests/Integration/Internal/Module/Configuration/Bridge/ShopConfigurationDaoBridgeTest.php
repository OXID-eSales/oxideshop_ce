<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ShopConfigurationDaoBridgeTest extends TestCase
{
    use ContainerTrait;

    public function testSaving()
    {
        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $someModule = new ModuleConfiguration();
        $someModule
            ->setId('someId')
            ->setPath('somePath');

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($someModule);

        $shopConfigurationDaoBridge->save($shopConfiguration);

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDaoBridge->get()
        );
    }
}
