<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
class TestEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handleEvent(Event $event)
    {
        return $event;
    }

    public static function getSubscribedEvents()
    {
        return ['eventname' => 'handleEvent'];
    }
}
