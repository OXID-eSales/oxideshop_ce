<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Locale\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Event\LocaleChangedEvent;
use PHPUnit\Framework\TestCase;

final class LocaleChangedEventTest extends TestCase
{
    public function testGetLocaleCode(): void
    {
        $event = new LocaleChangedEvent('de_DE');

        $this->assertSame('de_DE', $event->getLocaleCode());
    }
}
