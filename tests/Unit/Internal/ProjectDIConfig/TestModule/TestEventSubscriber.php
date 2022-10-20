<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
class TestEventSubscriber implements EventSubscriberInterface
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
