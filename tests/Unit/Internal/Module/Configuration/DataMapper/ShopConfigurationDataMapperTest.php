<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
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
            'moduleChains' => [
                Chain::CLASS_EXTENSIONS => [],
            ],
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
                Chain::CLASS_EXTENSIONS => [],
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
