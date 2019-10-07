<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;

class EventsDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'events';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasEvents()) {
            $data[self::MAPPING_KEY] = $this->getEvents($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setEvents($moduleConfiguration, $data[self::MAPPING_KEY]);
        }
        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $event
     */
    private function setEvents(ModuleConfiguration $moduleConfiguration, array $event): void
    {
        foreach ($event as $action => $method) {
            $moduleConfiguration->addEvent(new Event(
                $action,
                $method
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getEvents(ModuleConfiguration $configuration): array
    {
        $events = [];

        if ($configuration->hasEvents()) {
            foreach ($configuration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }
        }

        return $events;
    }
}
