<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\Configuration\Utility;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Exception\InvalidShopSettingValueException;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ShopSettingEncoderTest extends TestCase
{
    #[DataProvider('settingDataProvider')]
    public function testEncoding(bool|string|int|array $value, string|int $encodedValue, string $encodingType): void
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $encodedValue,
            $shopSettingEncoder->encode($encodingType, $value)
        );
    }

    #[DataProvider('settingDataProvider')]
    public function testDecoding(bool|string|int|array $value, string|int $encodedValue, string $encodingType): void
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $value,
            $shopSettingEncoder->decode($encodingType, $encodedValue)
        );
    }

    public function testEncodingInvalidValue(): void
    {
        $this->expectException(InvalidShopSettingValueException::class);
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->expectException(InvalidShopSettingValueException::class);
        $shopSettingEncoder->encode('object', new stdClass());
    }

    public static function settingDataProvider(): array
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
