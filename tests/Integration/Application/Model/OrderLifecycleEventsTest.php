<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\BeforePaymentExecuteEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\OrderCancelledEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\OrderCompletedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\OrderRecalculatedEvent;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class OrderLifecycleEventsTest extends IntegrationTestCase
{
    use CheckoutFixtureTrait;

    /** @var array<string, string[]> */
    private array $reported = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->removeCheckoutFixtures();
        $events = [OrderCompletedEvent::class, OrderCancelledEvent::class, OrderRecalculatedEvent::class];
        foreach ($events as $eventClass) {
            $this->reported[$eventClass] = [];
            $this->listen($eventClass, function (object $event): void {
                $this->reported[$event::class][] = $event->getOrder()->getId();
            });
        }
    }

    public function testFinalizedOrderIsReportedCompleted(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();

        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());

        $this->assertSame([$orderId], $this->reported[OrderCompletedEvent::class]);
    }

    public function testPendingOrderIsReportedCompletedOnceWhenItCompletes(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        $this->listen(BeforePaymentExecuteEvent::class, static function (BeforePaymentExecuteEvent $event): void {
            $event->setPending(CheckoutTestOrder::ORDER_STATE_OK);
        });
        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());
        $reportedAtCheckout = $this->reported[OrderCompletedEvent::class];
        $order = new CheckoutTestOrder();
        $order->load($orderId);

        $order->completeOrder();
        $order->completeOrder();

        $this->assertSame([[], [$orderId]], [$reportedAtCheckout, $this->reported[OrderCompletedEvent::class]]);
    }

    public function testCancelledOrderIsReported(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());
        $order = new CheckoutTestOrder();
        $order->load($orderId);

        $order->cancelOrder();

        $this->assertSame([$orderId], $this->reported[OrderCancelledEvent::class]);
    }

    public function testRecalculatedOrderIsReportedRecalculatedNotCompleted(): void
    {
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        (new CheckoutTestOrder())->finalizeOrder($basket, $basket->getBasketUser());
        Registry::getSession()->deleteVariable('sess_challenge');
        $order = new CheckoutTestOrder();
        $order->load($orderId);

        $order->recalculateOrder();

        $this->assertSame(
            [[$orderId], [$orderId]],
            [$this->reported[OrderRecalculatedEvent::class], $this->reported[OrderCompletedEvent::class]]
        );
    }
}
