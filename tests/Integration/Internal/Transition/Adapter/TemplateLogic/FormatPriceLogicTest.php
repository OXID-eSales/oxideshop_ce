<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Price;
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

    public function testFormatPriceWithInt(): void
    {
        $price = $this->formatPriceLogic->formatPrice(['price' => 1]);

        $this->assertEquals(
            '1,00 €',
            $price
        );
    }

    public function testFormatPriceWithNull()
    {
        $price = $this->formatPriceLogic->formatPrice(['price' => null]);

        $this->assertEquals(
            '',
            $price
        );
    }

    public function testFormatPriceWithIncorrectString(): void
    {
        $price = $this->formatPriceLogic->formatPrice(['price' => 'incorrect']);

        $this->assertEquals(
            '0,00 €',
            $price
        );
    }

    public function testFormatPriceWithIncorrectPriceObject(): void
    {
        $priceObject = new Price();
        $priceObject->setPrice(false);

        $price = $this->formatPriceLogic->formatPrice(['price' => $priceObject]);

        $this->assertEquals(
            '0,00 €',
            $price
        );
    }

    public function testFormatPriceWithCorrectPriceObject(): void
    {
        $priceObject = new Price();
        $priceObject->setPrice(120);

        $calculatedOxPrice = $this->formatPriceLogic->formatPrice(['price' => $priceObject]);

        $this->assertEquals(
            '120,00 €',
            $calculatedOxPrice
        );
    }

    public function testGetFormattedPriceWithEmptyCurrencyAndInteger(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => '',
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10.000,00',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithEmptyCurrencyAndNegativeInteger(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => '',
                'price' => -100
            ]
        );

        $this->assertEquals(
            '',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithCustomDecimalSeparator(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)['dec' => '-'],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10.000-00',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithCustomThousandSeparator(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)['thousand' => '-'],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10-000,00',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithCustomSign(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)['sign' => '$'],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10.000,00 $',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithCustomDecimalPlaces(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)['decimal' => 4],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10.000,0000',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithSignOnFront(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)[
                    'sign' => '$',
                    'side' => 'Front'
                ],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '$10.000,00',
            $formattedPrice
        );
    }

    public function testGetFormattedPriceWithSignOnIncorrectSide(): void
    {
        $formattedPrice = $this->formatPriceLogic->formatPrice(
            [
                'currency' => (object)[
                    'sign' => '$',
                    'side' => 'incorrect'
                ],
                'price' => 10_000
            ]
        );

        $this->assertEquals(
            '10.000,00 $',
            $formattedPrice
        );
    }
}
