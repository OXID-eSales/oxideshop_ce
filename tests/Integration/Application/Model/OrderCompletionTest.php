<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\BeforePaymentExecuteEvent;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class OrderCompletionTest extends IntegrationTestCase
{
    use CheckoutFixtureTrait;

    private string $orderId;

    private string $userId;

    private string $voucherId;

    public function setUp(): void
    {
        parent::setUp();

        $this->removeCheckoutFixtures();
        $basket = $this->createCheckoutBasketWithVoucher();
        $this->orderId = $this->startCheckout();
        $this->userId = $basket->getBasketUser()->getId();
        $this->voucherId = (string) array_key_first($basket->getVouchers());
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setPending(600);
        });
        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());
    }

    public function testCompletesThePendingOrderOnce(): void
    {
        $counterBefore = $this->getOrderCounter();
        $order = $this->loadPendingOrder();

        $firstResult = $order->completeOrder();
        $secondResult = $order->completeOrder();

        $this->assertSame(
            [Order::ORDER_STATE_OK, Order::ORDER_STATE_OK, 'OK', (string) ($counterBefore + 1), 1, 1],
            [
                $firstResult,
                $secondResult,
                $this->getStoredOrderField($this->orderId, 'oxtransstatus'),
                $this->getStoredOrderField($this->orderId, 'oxordernr'),
                $order->confirmations,
                $this->countRows('oxobject2group', "oxobjectid = ? AND oxgroupsid = 'oxidcustomer'", [$this->userId]),
            ]
        );
    }

    public function testStaleLoserOfTheCompletionRaceSendsNoSecondMail(): void
    {
        $counterBefore = $this->getOrderCounter();
        $winner = $this->loadPendingOrder();
        $loser = $this->loadPendingOrder();

        $winnerResult = $winner->completeOrder();
        $loserResult = $loser->completeOrder();

        $this->assertSame(
            [Order::ORDER_STATE_OK, Order::ORDER_STATE_OK, 1, 0, 'OK', $counterBefore + 1],
            [
                $winnerResult,
                $loserResult,
                $winner->confirmations,
                $loser->confirmations,
                $loser->getFieldData('oxtransstatus'),
                $this->getOrderCounter(),
            ]
        );
    }

    public function testCancelledPendingOrderCannotBeCompleted(): void
    {
        $counterBefore = $this->getOrderCounter();
        $order = $this->loadPendingOrder();
        $order->cancelOrder();

        $result = $order->completeOrder();

        $this->assertSame(
            [Order::ORDER_STATE_PAYMENTERROR, 'NOT_FINISHED', '0', 0, $counterBefore],
            [
                $result,
                $this->getStoredOrderField($this->orderId, 'oxtransstatus'),
                $this->getStoredOrderField($this->orderId, 'oxordernr'),
                $order->confirmations,
                $this->getOrderCounter(),
            ]
        );
    }

    public function testRemovedPendingOrderCannotBeCompleted(): void
    {
        $order = $this->loadPendingOrder();
        $order->delete();

        $result = $order->completeOrder();

        $this->assertSame(
            [Order::ORDER_STATE_PAYMENTERROR, 0, 0],
            [$result, $order->confirmations, $this->countRows('oxorder', 'oxid = ?', [$this->orderId])]
        );
    }

    public function testOrderInAnotherStatusCannotBeCompleted(): void
    {
        $this->storeOrderField($this->orderId, 'oxtransstatus', 'ERROR');
        $order = $this->loadPendingOrder();

        $result = $order->completeOrder();

        $this->assertSame(
            [Order::ORDER_STATE_PAYMENTERROR, 'ERROR', 0],
            [$result, $this->getStoredOrderField($this->orderId, 'oxtransstatus'), $order->confirmations]
        );
    }

    public function testAssignsTheTransactionId(): void
    {
        $order = $this->loadPendingOrder();

        $order->assignTransactionId('tx-1');
        $assigned = [
            $this->getStoredOrderField($this->orderId, 'oxtransid'),
            $order->getFieldData('oxtransid'),
            $this->getStoredOrderField($this->orderId, 'oxtransstatus'),
        ];
        $order->completeOrder();

        $this->assertSame(
            [['tx-1', 'tx-1', 'NOT_FINISHED'], 'tx-1'],
            [$assigned, $this->getStoredOrderField($this->orderId, 'oxtransid')]
        );
    }

    public function testMarksTheOrderPaidOnlyTheFirstTime(): void
    {
        $order = $this->loadPendingOrder();

        $this->freezeShopTime(1800000000);
        $order->markPaid();
        $this->freezeShopTime(1800003600);
        $order->markPaid();

        $this->assertSame(
            [date('Y-m-d H:i:s', 1800000000), date('Y-m-d H:i:s', 1800000000), 'NOT_FINISHED', ''],
            [
                $this->getStoredOrderField($this->orderId, 'oxpaid'),
                $order->getFieldData('oxpaid'),
                $this->getStoredOrderField($this->orderId, 'oxtransstatus'),
                $this->getStoredOrderField($this->orderId, 'oxtransid'),
            ]
        );
    }

    public function testCompletionMailSeesTheOrderedProducts(): void
    {
        $order = $this->loadPendingOrder();

        $order->completeOrder();

        $products = [];
        foreach ($order->getBasket()->getContents() as $basketItem) {
            $products[] = [$basketItem->getProductId(), $basketItem->getAmount()];
        }
        $this->assertSame([['checkout-article', 2.0]], $products);
    }

    public function testPendingOrderBindsTheVoucher(): void
    {
        $this->assertSame(
            [[$this->orderId, $this->userId, $this->getUsedDate()], 5.0],
            [$this->getVoucherBinding(), (float) $this->getStoredVoucherField($this->voucherId, 'oxdiscount')]
        );
    }

    public function testCompletionMailSeesTheVoucher(): void
    {
        $order = $this->loadPendingOrder();

        $order->completeOrder();

        $this->assertSame(
            [[$this->voucherId], 5.0, [$this->orderId, $this->userId, $this->getUsedDate()]],
            [
                array_keys($order->getVoucherList()),
                $order->getBasket()->getVoucherDiscount()->getBruttoPrice(),
                $this->getVoucherBinding(),
            ]
        );
    }

    public function testCancellingThePendingOrderRestoresTheStock(): void
    {
        $order = $this->loadPendingOrder();

        $order->cancelOrder();

        $this->assertSame(
            ['1', '999'],
            [$this->getStoredOrderField($this->orderId, 'oxstorno'), $this->getArticleStock()]
        );
    }

    public function testCancellingThePendingOrderKeepsItsVoucher(): void
    {
        $this->loadPendingOrder()->cancelOrder();

        $this->assertSame(
            [[$this->orderId, $this->userId, $this->getUsedDate()], '1'],
            [$this->getVoucherBinding(), $this->getStoredOrderField($this->orderId, 'oxstorno')]
        );
    }

    public function testReleasingTheVouchersOfACancelledPendingOrderFreesThem(): void
    {
        $order = $this->loadPendingOrder();
        $order->cancelOrder();

        $order->releaseVouchers();

        $this->assertSame(
            [['', '', ''], '0', ''],
            [
                $this->getVoucherBinding(),
                $this->getStoredVoucherField($this->voucherId, 'oxreserved'),
                $this->getStoredVoucherField($this->voucherId, 'oxdiscount'),
            ]
        );
    }

    public function testReleasingTheVouchersOfACompletedOrderKeepsThem(): void
    {
        $order = $this->loadPendingOrder();
        $order->completeOrder();

        $order->releaseVouchers();

        $this->assertSame([$this->orderId, $this->userId, $this->getUsedDate()], $this->getVoucherBinding());
    }

    public function testAssignsTheOrderNumberOnce(): void
    {
        $counterBefore = $this->getOrderCounter();
        $order = $this->loadPendingOrder();

        $firstNumber = $order->assignOrderNumber();
        $secondNumber = $order->assignOrderNumber();

        $this->assertSame(
            [$counterBefore + 1, $counterBefore + 1, $counterBefore + 1],
            [$firstNumber, $secondNumber, $this->getOrderCounter()]
        );
    }

    public function testOrderArticleOffersTheProductIcon(): void
    {
        $orderArticle = oxNew(OrderArticle::class);
        $orderArticle->load($this->loadPendingOrder()->getOrderArticles()->current()->getId());

        $this->assertSame($orderArticle->getArticle()->getIconUrl(), $orderArticle->getIconUrl());
    }

    private function loadPendingOrder(): CheckoutTestOrder
    {
        $order = new CheckoutTestOrder();
        $order->load($this->orderId);

        return $order;
    }

    /**
     * @return string[]
     */
    private function getVoucherBinding(): array
    {
        return [
            $this->getStoredVoucherField($this->voucherId, 'oxorderid'),
            $this->getStoredVoucherField($this->voucherId, 'oxuserid'),
            $this->getStoredVoucherField($this->voucherId, 'oxdateused'),
        ];
    }

    private function getUsedDate(): string
    {
        return date('Y-m-d', Registry::getUtilsDate()->getTime());
    }
}
