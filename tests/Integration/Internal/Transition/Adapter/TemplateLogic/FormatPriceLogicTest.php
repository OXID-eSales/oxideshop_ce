<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\Eshop\Core\Price;
use stdClass;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatPriceLogic;
use PHPUnit\Framework\TestCase;

final class FormatPriceLogicTest extends TestCase
{
    private FormatPriceLogic $formatPriceLogic;

    public function setUp(): void
    {
        parent::setUp();
        $this->formatPriceLogic = new FormatPriceLogic();
    }


    #[DataProvider('getFormatPriceProvider')]
    public function testFormatPrice(array $params, string $expected): void
    {
        $price = $this->formatPriceLogic->formatPrice($params);
        $this->assertEquals($expected, $price);
    }

    public static function getFormatPriceProvider(): array
    {
        return [
            [
                ['price' => 100],
                '100,00 €'
            ],
            [
                ['price' => null],
                ''
            ]
        ];
    }

    /**
     * @param mixed  $inputPrice
     */
    #[DataProvider('getCalculatePriceProvider')]
    public function testCalculatePrice(int|string|Price $inputPrice, string $expected): void
    {
        $params['price'] = $inputPrice;
        $calculatedOxPrice = $this->formatPriceLogic->formatPrice($params);
        $this->assertEquals($expected, $calculatedOxPrice);
    }

    public static function getCalculatePriceProvider(): array
    {
        $incorrectPriceObj = new Price();
        $incorrectPriceObj->setPrice(false);
        $correctPriceObj = new Price();
        $correctPriceObj->setPrice(120);

        return [
            [
                1, '1,00 €'
            ],
            [
                'incorrect', '0,00 €'
            ],
            [
                $incorrectPriceObj, '0,00 €'
            ],
            [
                $incorrectPriceObj, '0,00 €'
            ],
            [
                $correctPriceObj, '120,00 €'
            ]
        ];
    }

    #[DataProvider('getFormattedPriceProvider')]
    public function testGetFormattedPrice(string|stdClass $currency, int $price, string $expected): void
    {
        $params['currency'] = $currency;
        $params['price'] = $price;
        $formattedPrice = $this->formatPriceLogic->formatPrice($params);
        $this->assertEquals($expected, $formattedPrice);
    }

    public static function getFormattedPriceProvider(): array
    {
        $price = 10000;

        return [
            [
                '', $price, '10.000,00'
            ],
            [
                '', -100, ''
            ],
            [
                self::getCurrencyWithSeparator(['dec' => '-']), $price, '10.000-00'
            ],
            [
                self::getCurrencyWithSeparator(['thousand' => '-']), $price, '10-000,00'
            ],
            [
                self::getCurrencyWithSeparator(['sign' => '$']), $price, '10.000,00 $'
            ],
            [
                self::getCurrencyWithSeparator(['decimal' => 4]), $price, '10.000,0000'
            ],
            [
                self::getCurrencyWithSeparator(['sign' => '$', 'side' => 'Front']), $price, '$10.000,00'
            ],
            [
                self::getCurrencyWithSeparator(['sign' => '$', 'side' => 'incorrect']), $price, '10.000,00 $'
            ]
        ];
    }

    private static function getCurrencyWithSeparator(array $currency_array): stdClass
    {
        $currency = new stdClass();
        foreach ($currency_array as $key => $value) {
            $currency->$key = $value;
        }

        return $currency;
    }
}
