<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use Symfony\Contracts\EventDispatcher\Event;

class BasketChangedEvent extends Event
{
    /**
     * @deprecated constant will be removed in v7.0.
     */
    const NAME = self::class;

    /**
     * Url the shop wants to redirect to after product is put to basket.
     *
     * @var \OxidEsales\Eshop\Application\Component\BasketComponent
     */
    private $basketComponent;

    /**
     * BasketChangedEvent constructor.
     *
     * @param \OxidEsales\Eshop\Application\Component\BasketComponent $basketComponent Basket component
     */
    public function __construct(\OxidEsales\Eshop\Application\Component\BasketComponent $basketComponent)
    {
        $this->basketComponent = $basketComponent;
    }

    /**
     * Getter for basket component object.
     *
     * @return \OxidEsales\Eshop\Application\Component\BasketComponent
     */
    public function getBasket(): \OxidEsales\Eshop\Application\Component\BasketComponent
    {
        return $this->basketComponent;
    }
}
