<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

final class BasketTest extends IntegrationTestCase
{
    public static function providerBasketCalculation(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/basket/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }
        return $testCases;
    }

    #[DataProvider('providerBasketCalculation')]
    public function testBasketCalculation(array $testCase): void
    {
        // gathering data arrays
        $expected = $testCase['expected'];

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        // check basket item list
        $expectedProducts = $expected['articles'];
        $basketItemList = $basket->getContents();

        $this->assertCount(
            count($expectedProducts),
            $basketItemList,
            "Expected basket product amount doesn't match actual"
        );

        if ($basketItemList) {
            foreach ($basketItemList as $basketItem) {
                $productId = $basketItem->getArticle()
                    ->getID();
                $this->assertEquals(
                    $expectedProducts[$productId][0],
                    $basketItem->getFUnitPrice(),
                    "Unit price of product id {$productId}"
                );
                $this->assertEquals(
                    $expectedProducts[$productId][1],
                    $basketItem->getFTotalPrice(),
                    "Total price of product id {$productId}"
                );
            }
        }

        // Total discounts
        $expectedDiscounts = $expected['totals']['discounts'] ?? null;
        $expectedDiscountCount = (is_array($expectedDiscounts)) ? count($expectedDiscounts) : 0;
        $productDiscounts = $basket->getDiscounts();
        $productDiscountsCount = (is_array($productDiscounts)) ? count($productDiscounts) : 0;
        $this->assertEquals(
            $expectedDiscountCount,
            $productDiscountsCount,
            "Expected basket discount amount doesn't match actual"
        );
        if (! empty($expectedDiscounts)) {
            foreach ($productDiscounts as $discount) {
                $this->assertEquals(
                    $expectedDiscounts[$discount->sOXID],
                    $discount->fDiscount,
                    "Total discount of {$discount->sOXID}"
                );
            }
        }

        // Total vats
        $expectedVats = $expected['totals']['vats'] ?? null;
        $expectedVatsCount = (is_array($expectedVats)) ? count($expectedVats) : 0;
        $productVats = $basket->getProductVats();
        $productVatsCount = (is_array($productVats)) ? count($productVats) : 0;
        $this->assertEquals(
            $expectedVatsCount,
            $productVatsCount,
            "Expected basket different vat amount doesn't match actual"
        );
        if (! empty($expectedVats)) {
            foreach ($productVats as $percent => $sum) {
                $this->assertEquals($expectedVats[$percent], $sum, "Total Vat of {$percent}%");
            }
        }

        // Wrapping costs
        $expectedWrappings = $expected['totals']['wrapping'] ?? null;
        if (! empty($expectedWrappings)) {
            $this->assertEquals(
                $expectedWrappings['brutto'],
                $basket->getFWrappingCosts(),
                'Total wrappings brutto price'
            );
            $this->assertEquals(
                $expectedWrappings['netto'] ?? null,
                $basket->getWrappCostNet(),
                'Total wrappings netto price'
            );
            $this->assertEquals(
                $expectedWrappings['vat'] ?? null,
                $basket->getWrappCostVat(),
                'Total wrappings vat price'
            );
        }

        // Giftcard costs
        $expectedCards = $expected['totals']['giftcard'] ?? null;
        if (! empty($expectedCards)) {
            $this->assertEquals(
                $expectedCards['brutto'],
                $basket->getFGiftCardCosts(),
                'Total giftcard brutto price'
            );
            $this->assertEquals(
                $expectedCards['netto'] ?? null,
                $basket->getGiftCardCostNet(),
                'Total giftcard netto price'
            );
            $this->assertEquals(
                $expectedCards['vat'] ?? null,
                $basket->getGiftCardCostVat(),
                'Total giftcard vat price'
            );
        }

        // Delivery costs
        $expectedDeliveryCosts = $expected['totals']['delivery'] ?? null;
        if (! empty($expectedDeliveryCosts)) {
            $this->assertEquals(
                $expectedDeliveryCosts['brutto'],
                number_format(round($basket->getDeliveryCosts(), 2), 2, ',', '.'),
                'Delivery total brutto price'
            );
            $this->assertEquals(
                $expectedDeliveryCosts['netto'] ?? null,
                $basket->getDelCostNet(),
                'Delivery total netto price'
            );
            $this->assertEquals(
                $expectedDeliveryCosts['vat'] ?? null,
                $basket->getDelCostVat(),
                'Delivery total vat price'
            );
        }

        // Payment costs
        $expectedPayments = $expected['totals']['payment'] ?? null;
        if (! empty($expectedPayments)) {
            $this->assertEquals(
                $expectedPayments['brutto'] ?? null,
                number_format(round($basket->getPaymentCosts(), 2), 2, ',', '.'),
                'Payment total brutto price'
            );
            $this->assertEquals(
                $expectedPayments['netto'] ?? null,
                $basket->getPayCostNet(),
                'Payment total netto price'
            );
            $this->assertEquals(
                $expectedPayments['vat'] ?? null,
                $basket->getPayCostVat(),
                'Payment total vat price'
            );
        }

        // Vouchers
        $expectedVouchers = $expected['totals']['voucher'] ?? null;
        if (! empty($expectedVouchers)) {
            $this->assertEquals(
                $expectedVouchers['brutto'],
                number_format(round($basket->getVoucherDiscValue(), 2), 2, ',', '.'),
                'Voucher total discount brutto'
            );
        }

        // Total netto & brutto, grand total
        $this->assertEquals($expected['totals']['totalNetto'], $basket->getProductsNetPrice(), 'Total Netto');
        $this->assertEquals($expected['totals']['totalBrutto'], $basket->getFProductsPrice(), 'Total Brutto');
        $this->assertEquals($expected['totals']['grandTotal'], $basket->getFPrice(), 'Grand Total');
    }
}
