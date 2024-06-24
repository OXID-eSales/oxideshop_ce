<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Controller\Admin\OrderArticle;
use OxidEsales\Eshop\Application\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterAdminControllerOrderProductRenderEvent extends Event
{
    public const NAME = self::class;

    /** @var OrderArticle */
    private $controller;
    /** @var Order */
    private $order;

    public function __construct(
        OrderArticle $orderArticle,
        Order $order
    ) {
        $this->controller = $orderArticle;
        $this->order = $order;
    }

    /** @return OrderArticle */
    public function getController(): OrderArticle
    {
        return $this->controller;
    }

    /** @return Order */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
