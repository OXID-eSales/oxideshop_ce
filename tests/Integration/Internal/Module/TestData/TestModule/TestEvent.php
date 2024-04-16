<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule;

use Symfony\Contracts\EventDispatcher\Event;

class TestEvent extends Event
{
    private bool $handled = false;

    public function handle(): void
    {
        $this->handled = true;
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }
}
