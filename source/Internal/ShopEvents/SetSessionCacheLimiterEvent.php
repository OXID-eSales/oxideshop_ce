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
    protected $limiterIsSet = false;

    /**
     * Setter for limiter status flag.
     *
     * @param string $result
     */
    public function markLimiterSet($flag)
    {
        $this->limiterIsSet = $flag;
    }

    /**
     * Getter for limiter set yes/no flag.
     *
     * @return bool
     */
    public function isLimiterSet()
    {
        return $this->limiterIsSet;
    }
}
