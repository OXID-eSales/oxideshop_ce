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
class AfterUserBasketLoadToBasketEvent extends Event
{
    public const NAME = self::class;

    /** @var UserBasket  */
    private $userBasket;
    /** @var Basket */
    private $basket;

    public function __construct(UserBasket $userBasket, Basket $basket)
    {
        $this->userBasket = $userBasket;
        $this->basket = $basket;
    }

    /** @return UserBasket */
    public function getUserBasket(): UserBasket
    {
        return $this->userBasket;
    }

    /** @return Basket */
    public function getBasket(): Basket
    {
        return $this->basket;
    }
}
