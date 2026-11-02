<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\BeforeOrderFinalizeEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\BeforePaymentExecuteEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Exception\PaymentRedirectException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class OrderFinalizeInterruptionTest extends IntegrationTestCase
{
    use CheckoutFixtureTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->removeCheckoutFixtures();
    }

    public function testFinalizesTheOrderWhenNobodyInterrupts(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $counterBefore = $this->getOrderCounter();
        $order = new CheckoutTestOrder();

        $result = $order->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_OK, 'OK', (string) ($counterBefore + 1), 1, 1],
            [
                $result,
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
                $this->getStoredOrderField($orderId, 'oxordernr'),
                $order->gateway->calls,
                $order->confirmations,
            ]
        );
    }

    public function testFinalizedOrderBindsTheVoucher(): void
    {
        $basket = $this->createCheckoutBasketWithVoucher();
        $voucherId = (string) array_key_first($basket->getVouchers());
        $orderId = $this->startCheckout();
        $order = new CheckoutTestOrder();

        $result = $order->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_OK, 'OK', $orderId, 5.0, 1, 1],
            [
                $result,
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
                $this->getStoredVoucherField($voucherId, 'oxorderid'),
                (float) $this->getStoredVoucherField($voucherId, 'oxdiscount'),
                $order->gateway->calls,
                $order->confirmations,
            ]
        );
    }

    public function testResultBeforeFinalizeStopsBeforeAnythingIsWritten(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $counterBefore = $this->getOrderCounter();
        $this->listen(BeforeOrderFinalizeEvent::class, static function (BeforeOrderFinalizeEvent $event): void {
            $event->setResult(Order::ORDER_STATE_INVALIDPAYMENT);
        });
        $order = new CheckoutTestOrder();

        $result = $order->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_INVALIDPAYMENT, 0, 0, $counterBefore, 0],
            [
                $result,
                $this->countRows('oxorder', 'oxid = ?', [$orderId]),
                $this->countRows('oxuserpayments', 'oxuserid = ?', [$basket->getBasketUser()->getId()]),
                $this->getOrderCounter(),
                $order->gateway->calls,
            ]
        );
    }

    public function testRedirectBeforeFinalizeStopsBeforeAnythingIsWritten(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforeOrderFinalizeEvent::class, static function (BeforeOrderFinalizeEvent $event): void {
            $event->setRedirectUrl('https://provider.example/start');
        });
        $order = new CheckoutTestOrder();

        $redirectUrl = $this->finalizeUntilRedirect($order, $basket);

        $this->assertSame(
            ['https://provider.example/start', 0, 0],
            [
                $redirectUrl,
                $this->countRows('oxorder', 'oxid = ?', [$orderId]),
                $order->gateway->calls,
            ]
        );
    }

    public function testRedirectAtPaymentKeepsTheUnfinishedOrder(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setRedirectUrl('https://provider.example/pay');
        });
        $order = new CheckoutTestOrder();

        $redirectUrl = $this->finalizeUntilRedirect($order, $basket);

        $this->assertSame(
            ['https://provider.example/pay', 'NOT_FINISHED', '0', '997', 0, 0],
            [
                $redirectUrl,
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
                $this->getStoredOrderField($orderId, 'oxordernr'),
                $this->getArticleStock(),
                $order->gateway->calls,
                $order->confirmations,
            ]
        );
    }

    public function testRedirectAtPaymentBindsTheBasketVoucher(): void
    {
        $basket = $this->createCheckoutBasketWithVoucher();
        $voucherId = (string) array_key_first($basket->getVouchers());
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setRedirectUrl('https://provider.example/pay');
        });

        $this->finalizeUntilRedirect(new CheckoutTestOrder(), $basket);

        $this->assertSame(
            [$orderId, $basket->getBasketUser()->getId(), 5.0, 'NOT_FINISHED'],
            [
                $this->getStoredVoucherField($voucherId, 'oxorderid'),
                $this->getStoredVoucherField($voucherId, 'oxuserid'),
                (float) $this->getStoredVoucherField($voucherId, 'oxdiscount'),
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
            ]
        );
    }

    public function testPaidResultAtPaymentFinalizesWithoutTheGateway(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setPaid();
        });
        $order = new CheckoutTestOrder();

        $result = $order->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_OK, 'OK', 0, 1],
            [
                $result,
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
                $order->gateway->calls,
                $order->confirmations,
            ]
        );
        $this->assertNotSame('0', $this->getStoredOrderField($orderId, 'oxordernr'));
    }

    public function testDeclinedResultAtPaymentRemovesTheOrder(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setRefused(Order::ORDER_STATE_INVALIDPAYMENT);
        });

        $result = (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_INVALIDPAYMENT, 0, 0, '999'],
            [
                $result,
                $this->countRows('oxorder', 'oxid = ?', [$orderId]),
                $this->countRows('oxuserpayments', 'oxuserid = ?', [$basket->getBasketUser()->getId()]),
                $this->getArticleStock(),
            ]
        );
    }

    public function testPendingResultAtPaymentClearsTheOrderRemark(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        Registry::getSession()->setVariable('ordrem', 'ring twice');
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setPending(600);
        });

        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            ['ring twice', null],
            [$this->getStoredOrderField($orderId, 'oxremark'), Registry::getSession()->getVariable('ordrem')]
        );
    }

    public function testDeclinedResultAtPaymentKeepsAStornoedOrder(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->getOrder()->cancelOrder();
            $event->setRefused(Order::ORDER_STATE_INVALIDPAYMENT);
        });

        $result = (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_INVALIDPAYMENT, '1', 'NOT_FINISHED', '999'],
            [
                $result,
                $this->getStoredOrderField($orderId, 'oxstorno'),
                $this->getStoredOrderField($orderId, 'oxtransstatus'),
                $this->getArticleStock(),
            ]
        );
    }

    public function testDeclinedResultAtPaymentLeavesTheVoucherUnbound(): void
    {
        $basket = $this->createCheckoutBasketWithVoucher();
        $voucherId = (string) array_key_first($basket->getVouchers());
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setRefused(Order::ORDER_STATE_INVALIDPAYMENT);
        });

        $result = (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame(
            [Order::ORDER_STATE_INVALIDPAYMENT, 0, '', '0'],
            [
                $result,
                $this->countRows('oxorder', 'oxid = ?', [$orderId]),
                $this->getStoredVoucherField($voucherId, 'oxorderid'),
                $this->getStoredVoucherField($voucherId, 'oxreserved'),
            ]
        );
    }

    public function testRecalculationIsNotInterrupted(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());
        Registry::getSession()->deleteVariable('sess_challenge');
        $interruptions = 0;
        $countInterruption = static function () use (&$interruptions): void {
            $interruptions++;
        };
        $this->listen(BeforeOrderFinalizeEvent::class, $countInterruption);
        $this->listen(BeforePaymentExecuteEvent::class, $countInterruption);
        $order = new CheckoutTestOrder();
        $order->load($orderId);

        $order->recalculateOrder();

        $this->assertSame([0, 'OK'], [$interruptions, $this->getStoredOrderField($orderId, 'oxtransstatus')]);
    }

    private function finalizeUntilRedirect(CheckoutTestOrder $order, Basket $basket): string
    {
        try {
            $order->finalizeOrder($basket, $basket->getBasketUser());
        } catch (PaymentRedirectException $exception) {
            return $exception->getUrl();
        }

        $this->fail('finalizeOrder() did not ask for a redirect');
    }
}
