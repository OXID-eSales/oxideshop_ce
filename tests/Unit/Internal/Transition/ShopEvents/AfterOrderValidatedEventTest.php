<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterOrderValidatedEvent;
use PHPUnit\Framework\TestCase;

class AfterOrderValidatedEventTest extends TestCase
{
    public function testIsValidOrderDefaultValue(): void
    {
        $order = $this->createMock(Order::class);

        $result = (new AfterOrderValidatedEvent($order))->isValidOrder();

        $this->assertTrue($result);
    }

    public function testSetIsInvalidOrderWillReturnFalse(): void
    {
        $order = $this->createMock(Order::class);
        $event = new AfterOrderValidatedEvent($order);

        $event->setIsInvalidOrder();

        $this->assertFalse($event->isValidOrder());
    }

    public function testSetIsInvalidOrderWillStopPropagation(): void
    {
        $order = $this->createMock(Order::class);
        $event = new AfterOrderValidatedEvent($order);
        $this->assertFalse($event->isPropagationStopped());

        $event->setIsInvalidOrder();

        $this->assertTrue($event->isPropagationStopped());
    }

    public function testGetOrderStepDefaultValue(): void
    {
        $order = $this->createMock(Order::class);
        $event = new AfterOrderValidatedEvent($order);

        $this->assertSame(Order::ORDER_STATE_VOUCHERERROR, $event->getOrderStep());
    }

    public function testGetOrderStep(): void
    {
        $order = $this->createMock(Order::class);
        $event = new AfterOrderValidatedEvent($order);
        $orderStep = 12345;

        $event->setOrderStep($orderStep);

        $this->assertSame($orderStep, $event->getOrderStep());
    }
}
