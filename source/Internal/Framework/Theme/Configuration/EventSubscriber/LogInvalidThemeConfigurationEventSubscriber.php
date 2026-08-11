<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationInvalidEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogInvalidThemeConfigurationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function logInvalidConfiguration(ThemeConfigurationInvalidEvent $event): void
    {
        $this->logger->warning($event->getReason(), [
            'themeId' => $event->getThemeId(),
            'shopId' => $event->getShopId(),
        ]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeConfigurationInvalidEvent::class => 'logInvalidConfiguration',
        ];
    }
}
