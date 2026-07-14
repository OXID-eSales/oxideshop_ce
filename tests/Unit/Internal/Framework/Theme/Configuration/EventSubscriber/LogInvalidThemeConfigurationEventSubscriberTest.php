<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\EventSubscriber\LogInvalidThemeConfigurationEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationInvalidEvent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LogInvalidThemeConfigurationEventSubscriberTest extends TestCase
{
    public function testLogInvalidConfigurationLogsWarningWithThemeAndShopContext(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with('broken configuration', ['themeId' => 'apex', 'shopId' => 1]);

        $subscriber = new LogInvalidThemeConfigurationEventSubscriber($logger);
        $subscriber->logInvalidConfiguration(new ThemeConfigurationInvalidEvent('apex', 1, 'broken configuration'));
    }

    public function testSubscribesToThemeConfigurationInvalidEvent(): void
    {
        $events = LogInvalidThemeConfigurationEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(ThemeConfigurationInvalidEvent::class, $events);
        $this->assertSame('logInvalidConfiguration', $events[ThemeConfigurationInvalidEvent::class]);
    }
}
