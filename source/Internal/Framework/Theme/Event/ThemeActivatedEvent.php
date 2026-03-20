<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ThemeActivatedEvent extends Event
{
    public function __construct(private readonly int $shopId, private readonly string $themeId)
    {
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }
}
