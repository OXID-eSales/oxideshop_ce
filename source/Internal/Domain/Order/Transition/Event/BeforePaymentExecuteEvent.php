<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;

class BeforePaymentExecuteEvent extends Event
{
    private bool $paid = false;

    private bool $pending = false;

    private bool $refused = false;

    private int $result = Order::ORDER_STATE_PAYMENTERROR;

    private string $redirectUrl = '';

    public function __construct(
        private readonly Order $order,
        private readonly Basket $basket
    ) {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }

    public function setPaid(): void
    {
        $this->paid = true;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setRedirectUrl(string $url): void
    {
        $this->redirectUrl = $url;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function setPending(int $orderState): void
    {
        $this->result = $orderState;
        $this->pending = true;
    }

    public function isPending(): bool
    {
        return $this->pending;
    }

    public function setRefused(int $orderState): void
    {
        $this->result = $orderState;
        $this->refused = true;
    }

    public function isRefused(): bool
    {
        return $this->refused;
    }

    public function getResult(): int
    {
        return $this->result;
    }
}
