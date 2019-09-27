<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use Symfony\Component\EventDispatcher\Event;

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
