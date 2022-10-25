<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @deprecated will be removed completely in 7.0. All module services will be "shop aware" (available only in shops where the module is active) by default.
 */
class ShopAwareEventDispatcher extends EventDispatcher
{
    /**
     * Triggers the listeners of an event.
     *
     * This method checks if this event should be called within the given shop
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param object     $event     The event object to pass to the event handlers/listeners
     */
    protected function callListeners(iterable $listeners, string $eventName, object $event)
    {
        foreach ($listeners as $listener) {
            if ($this->eventPropagationWasStopped($event)) {
                break;
            }
            if ($this->skipListenerForCurrentShop($listener)) {
                continue;
            }
            $listener($event, $eventName, $this);
        }
    }

    private function eventPropagationWasStopped($event): bool
    {
        return $event instanceof StoppableEventInterface  && $event->isPropagationStopped();
    }

    private function skipListenerForCurrentShop($listener): bool
    {
        return is_array($listener)
            && is_object($listener[0])
            && in_array(ShopAwareInterface::class, class_implements($listener[0]))
            && !$listener[0]->isActive();
    }
}
