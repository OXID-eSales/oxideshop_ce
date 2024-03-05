<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TestEvent extends Event
{
    private int $counter = 0;

    public function getNumberOfActiveHandlers(): int
    {
        return $this->counter;
    }

    public function handleEvent(): void
    {
        $this->counter++;
    }
}
