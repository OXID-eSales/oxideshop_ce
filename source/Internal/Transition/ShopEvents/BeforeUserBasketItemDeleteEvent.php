<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\UserBasketItem;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeUserBasketItemDeleteEvent extends Event
{
    public const NAME = self::class;

    /** @var UserBasketItem */
    private $userBasketItem;

    public function __construct(UserBasketItem $userBasketItem)
    {
        $this->userBasketItem = $userBasketItem;
    }

    /** @return UserBasketItem */
    public function getUserBasketItem(): UserBasketItem
    {
        return $this->userBasketItem;
    }
}
