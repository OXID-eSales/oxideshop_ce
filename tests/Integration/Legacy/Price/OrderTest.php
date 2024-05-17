<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\DeliveryList;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

final class OrderTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->truncateDatabaseData();
    }

    public static function providerOrderCalculation(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/order/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }

        return $testCases;
    }

    #[DataProvider('providerOrderCalculation')]
    public function testOrderCalculation(array $testCase): void
    {
        $expected = $testCase['expected'];
        $actions = $testCase['actions'] ?? null;

        $basket = (new BasketConstruct())->calculateBasket($testCase);
        $user = $basket->getBasketUser();
        $order = $this->getOrderMock();

        if ($basket->getProductsCount()) {
            $success = $order->finalizeOrder($basket, $user);
            $this->assertEquals(0, $success);
        }

        $this->checkTotals($expected, 1, $order);
        if (!empty($actions)) {
            foreach ($actions as $function => $parameters) {
                match ($function) {
                    'changeConfigs' => $this->changeConfigs($parameters),
                    'changeArticles' => $this->changeProducts($parameters, $order),
                    'addArticles' => $this->addProducts($parameters, $order),
                    'removeArticles' => $this->removeProducts($parameters, $order),
                };
            }
            Registry::set(DeliveryList::class, null);
            $order->recalculateOrder();
            $this->checkTotals($expected, 2, $order);
        }
    }

    private function truncateDatabaseData(): void
    {
        $database = DatabaseProvider::getDb();
        $database->execute('DELETE FROM oxarticles WHERE 1');
        $database->execute('DELETE FROM oxcategories WHERE 1');
        $database->execute('DELETE FROM oxdiscount WHERE 1');
        $database->execute('DELETE FROM oxobject2discount WHERE 1');
        $database->execute('DELETE FROM oxwrapping WHERE 1');
        $database->execute('DELETE FROM oxdelivery WHERE 1');
        $database->execute('DELETE FROM oxdel2delset WHERE 1');
        $database->execute('DELETE FROM oxobject2payment WHERE 1');
        $database->execute('DELETE FROM oxobject2category WHERE 1');
        $database->execute('DELETE FROM oxvouchers WHERE 1');
        $database->execute('DELETE FROM oxvoucherseries WHERE 1');
        $database->execute('DELETE FROM oxuser WHERE 1');
        $database->execute('DELETE FROM oxdeliveryset WHERE 1');
        $database->execute('DELETE FROM oxpayments WHERE 1');
        $database->execute('DELETE FROM oxprice2article WHERE 1');
    }

    private function checkTotals(array $expected, int $iteration, Order $order): void
    {
        $expectedTotals = $expected[$iteration]['totals'];
        $products = $expected[$iteration]['articles'];
        $isNettoMode = $order->isNettoMode();
        $orderProduct = $order->getOrderArticles();

        foreach ($orderProduct as $product) {
            $productId = $product->getFieldData('oxartid');
            if ($isNettoMode) {
                $unitPrice = $product->getNetPriceFormated();
                $totalPrice = $product->getTotalNetPriceFormated();
            } else {
                $unitPrice = $product->getBrutPriceFormated();
                $totalPrice = $product->getTotalBrutPriceFormated();
            }
            if (isset($products[$productId])) {
                $this->assertEquals(
                    $products[$productId][0],
                    $unitPrice,
                    "#$iteration Unit price of order art no #{$productId}"
                );
                $this->assertEquals(
                    $products[$productId][1],
                    $totalPrice,
                    "#$iteration Total price of order art no #{$productId}"
                );
            }
        }

        $productVats = $order->getProductVats(true);

        $this->assertEquals(
            $expectedTotals['totalNetto'],
            $order->getFormattedTotalNetSum(),
            "Product Net Price #$iteration"
        );
        $this->assertEquals($expectedTotals['discount'], $order->getFormattedDiscount(), "Discount #$iteration");

        if ($productVats) {
            foreach ($productVats as $vat => $vatPrice) {
                $this->assertEquals($expectedTotals['vats'][$vat], $vatPrice, "Vat %{$vat} total cost #$iteration");
            }
        }

        $this->assertEquals(
            $expectedTotals['totalBrutto'],
            $order->getFormattedTotalBrutSum(),
            "Product Gross Price #$iteration"
        );

        if ($expectedTotals['voucher'] ?? null) {
            $this->assertEquals(
                $expectedTotals['voucher']['brutto'],
                $order->getFormattedTotalVouchers(),
                "Voucher costs #$iteration"
            );
        }

        if ($expectedTotals['delivery'] ?? null) {
            $this->assertEquals(
                $expectedTotals['delivery']['brutto'],
                $order->getFormattedDeliveryCost(),
                "Shipping costs #$iteration"
            );
        }

        if ($expectedTotals['wrapping'] ?? null) {
            $this->assertEquals(
                $expectedTotals['wrapping']['brutto'],
                $order->getFormattedWrapCost(),
                "Wrapping costs #$iteration"
            );
        }

        if ($expectedTotals['giftcard'] ?? null) {
            $this->assertEquals(
                $expectedTotals['giftcard']['brutto'],
                $order->getFormattedGiftCardCost(),
                "Giftcard costs #$iteration"
            );
        }

        if ($expectedTotals['payment'] ?? null) {
            $this->assertEquals(
                $expectedTotals['payment']['brutto'],
                $order->getFormattedPayCost(),
                "Charge Payment Method #$iteration"
            );
        }
        $this->assertEquals(
            $expectedTotals['grandTotal'],
            $order->getFormattedTotalOrderSum(),
            "Sum total #$iteration"
        );
    }

    /* --- Expected functions for changing saved order --- */

    private function changeConfigs(array $configOptions): void
    {
        if (!empty($configOptions)) {
            foreach ($configOptions as $sKey => $sValue) {
                Registry::getConfig()->setConfigParam($sKey, $sValue);
            }
        }
    }

    private function addProducts(array $productsData, Order $order): void
    {
        $products = (new BasketConstruct())->createProducts($productsData);
        foreach ($products as $productData) {
            $product = oxNew(Article::class);
            $product->load($productData['id']);
            $amount = $productData['amount'];
            $orderProduct = oxNew(OrderArticle::class);
            $orderProduct->oxorderarticles__oxartid = new Field($product->getId());
            $orderProduct->oxorderarticles__oxartnum = new Field($product->oxarticles__oxartnum->value);
            $orderProduct->oxorderarticles__oxamount = new Field($amount);
            $orderProduct->oxorderarticles__oxselvariant = new Field(
                Registry::getRequest()->getRequestEscapedParameter('sel')
            );
            $order->recalculateOrder([$orderProduct]);
        }
    }

    private function removeProducts(array $productIds, $order): void
    {
        foreach ($order->getOrderArticles() as $orderProduct) {
            foreach ($productIds as $productId) {
                if ((int)$orderProduct->getFieldData('oxartid') === (int)$productId) {
                    $orderProduct->delete();
                }
            }
        }
    }

    private function changeProducts(array $productAmounts, $order): void
    {
        foreach ($order->getOrderArticles() as $orderProduct) {
            foreach ($productAmounts as $productAmount) {
                if ((int)$orderProduct->getFieldData('oxartid') === (int)$productAmount['oxid']) {
                    $orderProduct->setNewAmount($productAmount['amount']);
                }
            }
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
