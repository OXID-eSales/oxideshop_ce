<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event;

use Symfony\Component\EventDispatcher\Event;

class TestEvent extends Event
{
    private $counter = 0;

    public function getNumberOfActiveHandlers()
    {
        return $this->counter;
    }

    public function handleEvent()
    {
        $this->counter++;
    }
}
