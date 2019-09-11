<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Converter;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\ModuleSettingsBooleanConverter;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use PHPUnit\Framework\TestCase;

class ModuleSettingsBooleanConverterTest extends TestCase
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
                MetaDataProvider::METADATA_SETTINGS => [
                    [
                        'type' => 'bool', 'value' => $value
                    ],
                ]
            ];
        $converter = new ModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertTrue($convertedSettings[MetaDataProvider::METADATA_SETTINGS][0]['value']);
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
                MetaDataProvider::METADATA_SETTINGS => [
                    [
                        'type' => 'bool', 'value' => $value
                    ],
                ]
            ];
        $converter = new ModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertFalse($convertedSettings[MetaDataProvider::METADATA_SETTINGS][0]['value']);
    }

    public function whenNothingToConvertDataProvider()
    {
        return [
            [[]],
            [
                [
                    MetaDataProvider::METADATA_SETTINGS => [
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
        $converter = new ModuleSettingsBooleanConverter();

        $convertedSettings = $converter->convert($metaData);
        $this->assertSame($metaData, $convertedSettings);
    }
}
