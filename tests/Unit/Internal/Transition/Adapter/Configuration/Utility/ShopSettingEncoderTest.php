<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Utility;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopSettingEncoderTest extends TestCase
{
    /**
     * @dataProvider settingDataProvider
     */
    public function testEncoding($value, $encodedValue, string $encodingType)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $encodedValue,
            $shopSettingEncoder->encode($encodingType, $value)
        );
    }

    /**
     * @dataProvider settingDataProvider
     */
    public function testDecoding($value, $encodedValue, string $encodingType)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $value,
            $shopSettingEncoder->decode($encodingType, $encodedValue)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Config\Exception\InvalidShopSettingValueException
     */
    public function testEncodingInvalidValue()
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $shopSettingEncoder->encode('object', new \stdClass());
    }

    public function settingDataProvider(): array
    {
        return [
            [
                true,
                '1',
                'bool'
            ],
            [
                'some string',
                'some string',
                'string'
            ],
            [
                2,
                2,
                'int'
            ],
            [
                ['value'],
                serialize(['value']),
                'arr'
            ],
            [
                ['value'],
                serialize(['value']),
                'aarr'
            ],
        ];
    }
}
