<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event;

use OxidEsales\Eshop\Application\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCancelledEvent extends Event
{
    public function __construct(private readonly Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
