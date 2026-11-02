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

class BeforeOrderFinalizeEvent extends Event
{
    private bool $interrupted = false;

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

    public function setRedirectUrl(string $url): void
    {
        $this->redirectUrl = $url;
        $this->interrupted = true;
    }

    public function setResult(int $orderState): void
    {
        $this->result = $orderState;
        $this->interrupted = true;
    }

    public function isInterrupted(): bool
    {
        return $this->interrupted;
    }

    public function getResult(): int
    {
        return $this->result;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
