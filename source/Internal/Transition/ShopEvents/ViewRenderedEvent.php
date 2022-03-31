<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Core\ShopControl;
use Symfony\Contracts\EventDispatcher\Event;

class ViewRenderedEvent extends Event
{
    public const NAME = self::class;

    public function __construct(private ShopControl $shopControl)
    {
    }

    /**
     * Getter for ShopControl object.
     *
     * @return ShopControl
     */
    public function getShopControl(): ShopControl
    {
        return $this->shopControl;
    }
}
