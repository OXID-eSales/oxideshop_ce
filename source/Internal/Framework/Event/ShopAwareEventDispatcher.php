<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

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
    protected function callListeners(iterable $listeners, string $eventName, object $event): void
    {
        $stoppable = $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            if (
                \is_array($listener) &&
                \is_object($listener[0]) &&
                \in_array(ShopAwareInterface::class, class_implements($listener[0]), true) &&
                !$listener[0]->isActive()
            ) {
                continue;
            }
            $listener($event, $eventName, $this);
        }
    }
}
