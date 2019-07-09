<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ModuleConfigurationDaoBridge;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

class ModuleConfigurationDaoBridgeTest extends TestCase
{
    public function testGet()
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

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao);
        $bridge->get('testModuleId');
    }

    public function testSave()
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getCurrentShopId')
            ->willReturn(1799);

        $moduleConfiguration = new ModuleConfiguration();

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->expects($this->once())
            ->method('save')
            ->with($moduleConfiguration, 1799);

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao);
        $bridge->save($moduleConfiguration);
    }
}
