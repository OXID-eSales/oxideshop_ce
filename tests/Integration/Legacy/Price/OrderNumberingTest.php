<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

final class OrderNumberingTest extends IntegrationTestCase
{
    public static function providerOrderNumbering(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/order_numbering/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }

        return $testCases;
    }

    /**
     * Tests order numbering with separateNumbering parameter.
     */
    #[DataProvider('providerOrderNumbering')]
    public function testOrderNumberingForDifferentShops(array $testCase): void
    {
        $options = $testCase['options'];

        $basket = (new BasketConstruct())->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order2->setSeparateNumbering($options['separateNumbering']);

        if ($basket->getProductsCount()) {
            $order2->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order2Nr = $order2->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order2Nr, 'Second order must start from beginning if separate numbering.');
        } else {
            $this->assertEquals(
                $order1Nr,
                ($order2Nr - 1),
                'Second order must had bigger number if no separate numbering.'
            );
        }
    }

    /**
     * Tests order numbering when middle one is deleted.
     */
    #[DataProvider('providerOrderNumbering')]
    public function testOrderNumberingForDifferentShops2(array $testCase): void
    {
        $options = $testCase['options'];

        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();

        if ($basket->getProductsCount()) {
            $order2->finalizeOrder($basket, $user);
        }

        $order2->delete();

        $order3 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order3->setSeparateNumbering($options['separateNumbering']);

        if ($basket->getProductsCount()) {
            $order3->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order3Nr = $order3->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order3Nr, 'Second order must start from beginning if separate numbering.');
        } else {
            $this->assertEquals(
                $order1Nr,
                ($order3Nr - 2),
                'Second order must had bigger number if no separate numbering.'
            );
        }
    }

    /**
     * Tests order numbering when middle one is saved without finalizing.
     */
    #[DataProvider('providerOrderNumbering')]
    public function testOrderNumberingForDifferentShops3(array $testCase): void
    {
        $options = $testCase['options'];

        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();
        $order2->save();

        $order3 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order3->setSeparateNumbering($options['separateNumbering']);

        if ($basket->getProductsCount()) {
            $order3->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order3Nr = $order3->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order3Nr, 'Second order must start from beginning if separate numbering.');
        } else {
            $this->assertEquals(
                $order1Nr,
                ($order3Nr - 1),
                'Second order must had bigger number if no separate numbering.'
            );
        }
    }

    private function getOrderMock(): Order
    {
        $order = $this->createPartialMock(
            Order::class,
            ['sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery', 'validatePayment', 'getCoreTableName']
        );
        $order->method('sendOrderByEmail')->willReturn(0);
        $order->method('getCoreTableName')->willReturn('oxorder');

        return $order;
    }
}
