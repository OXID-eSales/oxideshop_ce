<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Adapter\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Service\ShopSettingEncoder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopSettingEncoderTest extends TestCase
{
    /**
     * @dataProvider encodingTypeDataProvider
     */
    public function testEncodingTypeGetter($value, string $expectedType)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $expectedType,
            $shopSettingEncoder->getEncodingType($value)
        );
    }

    /**
     * @dataProvider settingDataProvider
     */
    public function testEncoding($value, string $encodedValue)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $encodedValue,
            $shopSettingEncoder->encode($value)
        );
    }

    /**
     * @dataProvider settingDataProvider
     */
    public function testDecoding($value, string $encodedValue, string $encodingType)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $value,
            $shopSettingEncoder->decode($encodingType, $encodedValue)
        );
    }

    public function encodingTypeDataProvider(): array
    {
        return [
            [
                true,
                'bool'
            ],
            [
                'some string',
                'str'
            ],
            [
                2,
                'num'
            ],
            [
                ['value'],
                'arr'
            ],
        ];
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
                '2',
                'num'
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
