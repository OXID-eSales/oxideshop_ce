<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ThemeConfigurationInvalidEvent extends Event
{
    public function __construct(
        private string $themeId,
        private int $shopId,
        private string $reason,
    ) {
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
