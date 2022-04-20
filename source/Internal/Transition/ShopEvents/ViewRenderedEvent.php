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
    /**
     * @deprecated constant will be removed in v7.0.
     */
    const NAME = self::class;

    /**
     * @var ShopControl
     */
    private $shopControl;

    /**
     * Class constructor.
     *
     * @param ShopControl $shopControl ShopControl object
     */
    public function __construct(ShopControl $shopControl)
    {
        $this->shopControl = $shopControl;
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
