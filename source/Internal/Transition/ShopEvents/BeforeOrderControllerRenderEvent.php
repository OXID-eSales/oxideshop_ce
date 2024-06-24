<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Basket;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeOrderControllerRenderEvent extends Event
{
    public const NAME = self::class;

    /** @var OrderController */
    private $orderController;
    /** @var Basket */
    private $basket;

    public function __construct(
        OrderController $orderController,
        Basket $basket
    ) {
        $this->orderController = $orderController;
        $this->basket = $basket;
    }

    /** @return OrderController */
    public function getController(): OrderController
    {
        return $this->orderController;
    }

    /** @return Basket */
    public function getBasket(): Basket
    {
        return $this->basket;
    }
}
