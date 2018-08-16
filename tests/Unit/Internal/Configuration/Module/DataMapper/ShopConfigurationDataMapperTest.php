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
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationDataMapperTest extends TestCase
{
    public function testModulesMappingFromData()
    {
        $configurationData = [
            'modules' => [
                'happyModule' => [],
                'funnyModule' => [],
            ],
        ];

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->setModuleConfiguration('happyModule', new ModuleConfiguration());
        $shopConfiguration->setModuleConfiguration('funnyModule', new ModuleConfiguration());

        $shopConfigurationDataMapper = new ShopConfigurationDataMapper(
            $this->getModuleConfigurationMapper()
        );

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDataMapper->fromData($configurationData)
        );
    }

    public function testChainsMappingFromData()
    {
        $configurationData = [
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

        $chain = $shopConfiguration
            ->getChainGroup('blocks')
            ->getChain('testBlock');

        $this->assertSame('testBlock', $chain->getId());
        $this->assertSame(
            [
                'secondBlock',
                'thirdBlock',
            ],
            $chain->getChain()
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

        return $moduleConfigurationDataMapper;
    }
}
