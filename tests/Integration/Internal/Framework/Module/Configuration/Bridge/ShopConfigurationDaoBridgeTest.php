<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ShopConfigurationDaoBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var string
     */
    private $testModuleId = 'testModuleId';

    public function testSaving(): void
    {
        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $someModule = new ModuleConfiguration();
        $someModule
            ->setId('someId')
            ->setModuleSource('test');

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($someModule);

        $shopConfigurationDaoBridge->save($shopConfiguration);

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDaoBridge->get()
        );
    }
}
