<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ShopConfigurationDaoBridgeTest extends TestCase
{
    use ContainerTrait;

    public function testGet()
    {
        $context = $this->get(ContextInterface::class);
        $projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);

        $currentShopConfiguration = $projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration($context->getEnvironment())
            ->getShopConfiguration($context->getCurrentShopId());

        $this->assertEquals(
            $currentShopConfiguration,
            $this->get(ShopConfigurationDaoBridgeInterface::class)->get()
        );
    }

    public function testSave()
    {
        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $someModule = new ModuleConfiguration();
        $someModule
            ->setId('someId')
            ->setPath('somePath');

        $currentShopConfiguration = $shopConfigurationDaoBridge->get();
        $currentShopConfiguration->addModuleConfiguration($someModule);

        $shopConfigurationDaoBridge->save($currentShopConfiguration);

        $this->assertEquals(
            $currentShopConfiguration,
            $shopConfigurationDaoBridge->get()
        );
    }
}
