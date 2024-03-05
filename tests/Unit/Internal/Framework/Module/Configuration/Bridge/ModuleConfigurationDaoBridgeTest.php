<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class ModuleConfigurationDaoBridgeTest extends TestCase
{
    public function testGet(): void
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getCurrentShopId')
            ->willReturn(1789);

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->expects($this->once())
            ->method('get')
            ->with('testModuleId', 1789);

        $shopEnvironmentConfigurationDao =
            $this->getMockBuilder(ModuleEnvironmentConfigurationDaoInterface::class)->getMock();

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao, $shopEnvironmentConfigurationDao);
        $bridge->get('testModuleId');
    }

    public function testSave(): void
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getCurrentShopId')
            ->willReturn(1799);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testId');

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->expects($this->once())
            ->method('save')
            ->with($moduleConfiguration, 1799);

        $shopEnvironmentConfigurationDao =
            $this->getMockBuilder(ModuleEnvironmentConfigurationDaoInterface::class)->getMock();
        $shopEnvironmentConfigurationDao->expects($this->once())->method('remove');

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao, $shopEnvironmentConfigurationDao);
        $bridge->save($moduleConfiguration);
    }
}
