<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use Symfony\Contracts\EventDispatcher\Event;

class BasketChangedEvent extends Event
{
    public const NAME = self::class;

    /**
     * Url the shop wants to redirect to after product is put to basket.
     *
     * @var BasketComponent
     */
    private $basketComponent;

    /**
     * BasketChangedEvent constructor.
     *
     * @param BasketComponent $basketComponent Basket component
     */
    public function __construct(BasketComponent $basketComponent)
    {
        $this->basketComponent = $basketComponent;
    }

    /**
     * Getter for basket component object.
     *
     * @return BasketComponent
     */
    public function getBasket(): BasketComponent
    {
        return $this->basketComponent;
    }
}
