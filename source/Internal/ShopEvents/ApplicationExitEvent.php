<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplicationExitEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ApplicationExitEvent extends Event
{
    const NAME = self::class;

    /**
     * Result
     *
     * @var bool
     */
    private $result = false;

    /**
     * Setter for result.
     *
     * @param bool $result
     */
    public function setResult(bool $result)
    {
        $this->result = $result;
    }

    /**
     * Getter for result
     *
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }
}
