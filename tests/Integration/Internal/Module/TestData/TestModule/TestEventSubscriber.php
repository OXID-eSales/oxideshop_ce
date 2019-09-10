<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule;

use OxidEsales\EshopCommunity\Internal\Application\Events\AbstractShopAwareEventSubscriber;

/**
 * @internal
 */
class TestEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handleEvent(TestEvent $event)
    {
        $event->handle();
        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [TestEvent::NAME => 'handleEvent'];
    }
}
