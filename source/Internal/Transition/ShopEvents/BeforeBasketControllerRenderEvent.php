<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Controller\BasketController;
use OxidEsales\Eshop\Application\Model\Basket;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeBasketControllerRenderEvent extends Event
{
    public const NAME = self::class;

    /** @var BasketController */
    private $basketController;
    /**
     * @var Basket
     */
    private $basket;

    public function __construct(
        BasketController $basketController,
        Basket $basket
    ) {
        $this->basketController = $basketController;
        $this->basket = $basket;
    }

    /** @return BasketController */
    public function getController(): BasketController
    {
        return $this->basketController;
    }

    /** @return Basket */
    public function getBasket(): Basket
    {
        return $this->basket;
    }
}
