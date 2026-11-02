<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\OrderMain;
use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\OrderShippedEvent;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[RunTestsInSeparateProcesses]
final class OrderShippedEventTest extends IntegrationTestCase
{
    /** @var array<int, array{string, bool}> */
    private array $shipped = [];

    public function setUp(): void
    {
        parent::setUp();

        $order = oxNew(Order::class);
        $order->setId('shipped-order');
        $order->save();
        ContainerFacade::get(EventDispatcherInterface::class)->addListener(
            OrderShippedEvent::class,
            function (OrderShippedEvent $event): void {
                $storedSendDate = DatabaseProvider::getDb()->getOne(
                    'SELECT oxsenddate FROM oxorder WHERE oxid = ?',
                    [$event->getOrder()->getId()]
                );
                $this->shipped[] = [$event->getOrder()->getId(), $storedSendDate !== '0000-00-00 00:00:00'];
            }
        );
    }

    public function testSendingTheOrderFromTheMainTabReportsItShipped(): void
    {
        $controller = oxNew(OrderMain::class);
        $controller->setEditObjectId('shipped-order');

        $controller->sendOrder();

        $this->assertSame([['shipped-order', true]], $this->shipped);
    }

    public function testSendingTheOrderFromTheOverviewReportsItShipped(): void
    {
        $controller = oxNew(OrderOverview::class);
        $controller->setEditObjectId('shipped-order');

        $controller->sendorder();

        $this->assertSame([['shipped-order', true]], $this->shipped);
    }

    public function testAnUnknownOrderIsNotReported(): void
    {
        $controller = oxNew(OrderMain::class);
        $controller->setEditObjectId('no-such-order');

        $controller->sendOrder();

        $this->assertSame([], $this->shipped);
    }
}
