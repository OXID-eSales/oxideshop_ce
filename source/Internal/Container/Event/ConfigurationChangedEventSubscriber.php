<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container\Event;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationChangedEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerCache
     */
    private $containerCache;

    public function __construct(ContainerCache $containerCache)
    {
        $this->containerCache = $containerCache;
    }

    /**
     * @param ProjectYamlChangedEvent $event
     */
    public function resetContainer(ProjectYamlChangedEvent $event)
    {
        $this->containerCache->invalidate();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ProjectYamlChangedEvent::NAME => 'resetContainer'];
    }
}
