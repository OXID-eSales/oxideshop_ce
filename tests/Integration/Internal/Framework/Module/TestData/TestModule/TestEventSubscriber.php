<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TestData\TestModule;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\TestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class TestEventSubscriber implements EventSubscriberInterface
{
    public function handleEvent(TestEvent $event): TestEvent
    {
        $event->handle();
        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [TestEvent::class => 'handleEvent'];
    }
}
