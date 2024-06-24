<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterOrderValidatedEvent extends Event
{
    public const NAME = self::class;

    /** @var Order */
    private $order;
    /** @var Basket */
    private $basket;
    /** @var User */
    private $user;
    /** @var bool */
    private $isValidOrder = true;
    /** @var int  */
    private $orderStep = Order::ORDER_STATE_VOUCHERERROR;

    public function __construct(
        Order $order,
        Basket $basket,
        User $user
    ) {
        $this->order = $order;
        $this->basket = $basket;
        $this->user = $user;
    }

    /** @return Order */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /** @return bool */
    public function isValidOrder(): bool
    {
        return $this->isValidOrder;
    }

    public function setIsInvalidOrder(): void
    {
        $this->isValidOrder = false;
        $this->stopPropagation();
    }

    /** @return int */
    public function getOrderStep(): int
    {
        return $this->orderStep;
    }

    /** @param int $orderStep */
    public function setOrderStep(int $orderStep): void
    {
        $this->orderStep = $orderStep;
    }

    /** @return Basket */
    public function getBasket(): Basket
    {
        return $this->basket;
    }

    /** @return User */
    public function getUser(): User
    {
        return $this->user;
    }
}
