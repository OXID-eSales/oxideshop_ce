<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;

class TestEventSubscriber extends AbstractShopAwareEventSubscriber
{
    private $stopPropagation = false;

    public function __construct($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }

    public function handleEvent(TestEvent $event)
    {
        $event->handleEvent();
        if ($this->stopPropagation) {
            $event->stopPropagation();
        }

        return $event;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return ['oxidesales.testevent' => 'handleEvent'];
    }
}
