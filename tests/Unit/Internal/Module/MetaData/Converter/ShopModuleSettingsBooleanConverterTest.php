<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData\Converter;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter\ShopModuleSettingsBooleanConverter;
use PHPUnit\Framework\TestCase;

class ShopModuleSettingsBooleanConverterTest extends TestCase
{
    public function convertToTrueDataProvider()
    {

        return [
            ['true'],
            ['True'],
            ['1'],
            [1],
            [true],
        ];
    }

    /**
     * @param $value
     * @dataProvider convertToTrueDataProvider
     */
    public function testConvertToTrue($value): void
    {
        $metaData =
            [
                ModuleSetting::SHOP_MODULE_SETTING => [
                    [
                        'type' => 'bool', 'value' => $value
                    ],
                ]
            ];
        $converter = new ShopModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertTrue($convertedSettings[ModuleSetting::SHOP_MODULE_SETTING][0]['value']);
    }

    public function convertToFalseDataProvider()
    {

        return [
            ['false'],
            ['False'],
            ['0'],
            [0],
            [false],
        ];
    }

    /**
     * @param $value
     * @dataProvider convertToFalseDataProvider
     */
    public function testConvertToFalse($value): void
    {
        $metaData =
            [
                ModuleSetting::SHOP_MODULE_SETTING => [
                    [
                        'type' => 'bool', 'value' => $value
                    ],
                ]
            ];
        $converter = new ShopModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertFalse($convertedSettings[ModuleSetting::SHOP_MODULE_SETTING][0]['value']);
    }

    public function whenNothingToConvertDataProvider()
    {
        return [
            [[]],
            [
                [
                    ModuleSetting::SHOP_MODULE_SETTING => [
                        [
                            'type' => 'str', 'value' => 'any'
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $metaData
     * @dataProvider whenNothingToConvertDataProvider
     */
    public function testWhenNothingToConvert(array $metaData): void
    {
        $converter = new ShopModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertSame($metaData, $convertedSettings);
    }
}
