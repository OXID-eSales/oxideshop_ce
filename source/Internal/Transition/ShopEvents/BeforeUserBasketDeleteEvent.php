<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\UserBasket;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeUserBasketDeleteEvent extends Event
{
    public const NAME = self::class;

    /** @var UserBasket */
    private $userBasket;

    public function __construct(UserBasket $userBasket)
    {
        $this->userBasket = $userBasket;
    }

    /** @return UserBasket */
    public function getUserBasket(): UserBasket
    {
        return $this->userBasket;
    }
}
