<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ShopAwareEventDispatcher extends EventDispatcher
{
    /**
     * @param \callable[] $listeners
     * @param string      $eventName
     * @param Event       $event
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }
            if (is_array($listener) &&
                is_object($listener[0]) &&
                in_array(ShopAwareInterface::class, class_implements($listener[0])) &&
                ! $listener[0]->isActive()) {
                continue;
            }
            call_user_func($listener, $event, $eventName, $this);
        }
    }
}
