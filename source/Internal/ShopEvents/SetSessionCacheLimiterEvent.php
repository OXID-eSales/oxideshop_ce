<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SetSessionCacheLimiterEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class SetSessionCacheLimiterEvent extends Event
{
    const NAME = self::class;

    /**
     * Result
     *
     * @var bool
     */
    private $limiterIsSet = false;

    /**
     * Setter for limiter status flag.
     *
     * @param bool $flag Status flag
     */
    public function markLimiterSet(bool $flag)
    {
        $this->limiterIsSet = $flag;
    }

    /**
     * Getter for limiter set yes/no flag.
     *
     * @return bool
     */
    public function isLimiterSet(): bool
    {
        return $this->limiterIsSet;
    }
}
