<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeOrderDeleteEvent extends Event
{
    public const NAME = self::class;

    /** @var Order */
    private $order;

    public function __construct(
        Order $order
    ) {
        $this->order = $order;
    }

    /** @return Order */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
