<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\UserBasket;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterBasketSaveToUserBasketEvent extends Event
{
    public const NAME = self::class;

    /** @var Basket */
    private $basket;
    /** @var UserBasket  */
    private $userBasket;

    public function __construct(Basket $basket, UserBasket $userBasket)
    {
        $this->basket = $basket;
        $this->userBasket = $userBasket;
    }

    /** @return Basket */
    public function getBasket(): Basket
    {
        return $this->basket;
    }

    /** @return UserBasket */
    public function getUserBasket(): UserBasket
    {
        return $this->userBasket;
    }
}
