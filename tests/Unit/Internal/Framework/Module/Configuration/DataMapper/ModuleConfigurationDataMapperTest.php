<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataMapper;

use PHPUnit\Framework\Attributes\DataProvider;
use MyVendor\MyController\Controller1;
use MyVendor\MyController\Controller2;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassExtensionsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ControllersDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\EventsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ModuleConfigurationDataMapperTest extends TestCase
{
    use ContainerTrait;

    #[DataProvider('moduleConfigurationDataProvider')]
    public function testToDataAndFromData(array $data, ModuleConfigurationDataMapperInterface $dataMapper): void
    {

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration = $dataMapper->fromData($moduleConfiguration, $data);

        $this->assertEquals(
            $data,
            $dataMapper->toData($moduleConfiguration)
        );
    }

    public static function moduleConfigurationDataProvider(): array
    {
        return [
            [
                'data' => [
                    ClassExtensionsDataMapper::MAPPING_KEY => [
                        'shopClass1' => 'moduleClass1',
                        'shopClass2' => 'moduleClass2'
                    ]
                ],
                'dataMapper' => new ClassExtensionsDataMapper()

            ],
            [
                'data' => [
                    ControllersDataMapper::MAPPING_KEY => [
                        'controller1' => Controller1::class,
                        'controller2' => Controller2::class
                    ]
                ],
                'dataMapper' => new ControllersDataMapper()

            ],
            [
                'data' => [
                    EventsDataMapper::MAPPING_KEY => [
                            'onActivate'   => 'MyEvents::onActivate',
                            'onDeactivate' => 'MyEvents::onDeactivate'
                    ]
                ],
                'dataMapper' => new EventsDataMapper()

            ],
            [
                'data' => [
                    ModuleSettingsDataMapper::MAPPING_KEY => [
                        'testEmptyBoolConfig' => [
                            'group' => 'settingsEmpty',
                            'type' => 'bool',
                            'value' => 'false'
                        ],
                        'testFilledAArrConfig' => [
                            'group' => 'settingsFilled',
                            'type' => 'aarr',
                            'value' => ['key1' => 'option1', 'key2' => 'option2']
                        ]
                    ]
                ],
                'dataMapper' => new ModuleSettingsDataMapper()

            ]
        ];
    }
}
