<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

trait ShopAwareServiceTrait
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var array
     */
    private $activeShops;

    /**
     * This method is used by the DI container
     * to set an array of shop ids for which
     * this event subscriber should be executed.
     */
    public function setActiveShops(array $activeShops): void
    {
        $this->activeShops = $activeShops;
    }

    /**
     * This is set by the DI container to provide
     * access to the current shop ID to determine
     * if the event should be executed or not.
     */
    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    /**
     * This method is used by the event dispatcher to
     * determine, if the event should be executed for
     * the current shop or not.
     *
     * @return bool
     */
    public function isActive()
    {
        return \in_array((string)($this->context->getCurrentShopId()), $this->activeShops, true);
    }
}
