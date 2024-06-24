<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview;
use OxidEsales\Eshop\Application\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterAdminControllerOrderOverviewRenderEvent extends Event
{
    public const NAME = self::class;

    /** @var OrderOverview */
    private $controller;
    /** @var Order */
    private $order;

    public function __construct(
        OrderOverview $orderOverview,
        Order $order
    ) {
        $this->controller = $orderOverview;
        $this->order = $order;
    }

    /** @return OrderOverview */
    public function getController(): OrderOverview
    {
        return $this->controller;
    }

    /** @return Order */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
