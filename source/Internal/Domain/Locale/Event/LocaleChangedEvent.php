<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LocaleChangedEvent extends Event
{
    public function __construct(private readonly string $localeCode)
    {
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }
}
