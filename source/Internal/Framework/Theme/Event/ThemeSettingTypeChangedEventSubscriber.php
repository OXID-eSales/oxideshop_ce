<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ThemeSettingTypeChangedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function logTypeChange(ThemeSettingTypeChangedEvent $event): void
    {
        $this->logger->warning(
            'Theme setting type has changed. Setting value was replaced with the value from theme.php',
            [
                'shopId' => $event->getShopId(),
                'themeId' => $event->getThemeId(),
                'settingName' => $event->getSettingName(),
                'previousType' => $event->getPreviousType(),
                'type' => $event->getType(),
            ]
        );
    }

    /** @return string[] */
    public static function getSubscribedEvents(): array
    {
        return [ThemeSettingTypeChangedEvent::class => 'logTypeChange'];
    }
}
