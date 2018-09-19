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
     * @dataProvider settingDataProvider
     */
    public function testEncoding($value, string $encodedValue, string $encodingType)
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
    public function testDecoding($value, string $encodedValue, string $encodingType)
    {
        $shopSettingEncoder = new ShopSettingEncoder();

        $this->assertSame(
            $value,
            $shopSettingEncoder->decode($encodingType, $encodedValue)
        );
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
