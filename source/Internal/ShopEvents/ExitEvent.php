<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ExitEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ExitEvent extends Event
{
    const NAME = 'oxidesales.utils.prepareToExit';

    /**
     * Result
     *
     * @var bool
     */
    protected $result = false;

    /**
     * Setter for result.
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Getter for result
     *
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }
}
