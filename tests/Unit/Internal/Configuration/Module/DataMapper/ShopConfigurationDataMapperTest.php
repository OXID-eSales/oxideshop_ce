<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ShopConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationDataMapperTest extends TestCase
{
    public function testModulesMapping()
    {
        $configurationData = [
            'modules' => [
                'happyModule' => [],
                'funnyModule' => [],
            ],
            'moduleChains' => [],
        ];

        $shopConfigurationDataMapper = new ShopConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $shopConfiguration = $shopConfigurationDataMapper->fromData($configurationData);

        $this->assertEquals(
            $configurationData,
            $shopConfigurationDataMapper->toData($shopConfiguration)
        );
    }

    public function testChainsMapping()
    {
        $configurationData = [
            'modules'      => [],
            'moduleChains' => [
                'blocks' => [
                    'testBlock' => [
                        'secondBlock',
                        'thirdBlock',
                    ],
                ]
            ],
        ];

        $shopConfigurationDataMapper = new ShopConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $shopConfiguration = $shopConfigurationDataMapper->fromData($configurationData);

        $this->assertSame(
            $configurationData,
            $shopConfigurationDataMapper->toData($shopConfiguration)
        );
    }

    private function getModuleConfigurationMapper()
    {
        $moduleConfigurationDataMapper = $this
            ->getMockBuilder(ModuleConfigurationDataMapperInterface::class)
            ->getMock();

        $moduleConfigurationDataMapper
            ->method('fromData')
            ->willReturn(new ModuleConfiguration());

        $moduleConfigurationDataMapper
            ->method('toData')
            ->willReturn([]);

        return $moduleConfigurationDataMapper;
    }
}
