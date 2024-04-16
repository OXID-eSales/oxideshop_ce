<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolver;
use PHPUnit\Framework\TestCase;

final class ModulePathResolverTest extends TestCase
{
    public function testGetFullModulePathFromConfiguration(): void
    {
        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context
            ->method('getShopRootPath')
            ->willReturn('shopRoot');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleId')
            ->setModuleSource('vendor/modulePath');

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->method('get')
            ->with('testModuleId', 1)
            ->willReturn($moduleConfiguration);

        $pathResolver = new ModulePathResolver($moduleConfigurationDao, $context);

        $this->assertSame(
            'shopRoot/vendor/modulePath',
            $pathResolver->getFullModulePathFromConfiguration('testModuleId', 1)
        );
    }
}
