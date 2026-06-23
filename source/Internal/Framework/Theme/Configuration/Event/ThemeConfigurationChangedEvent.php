<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use Symfony\Contracts\EventDispatcher\Event;

class ThemeConfigurationChangedEvent extends Event
{
    public function __construct(
        private readonly ThemeConfiguration $themeConfiguration,
        private readonly int $shopId
    ) {
    }

    public function getThemeConfiguration(): ThemeConfiguration
    {
        return $this->themeConfiguration;
    }

    public function getThemeId(): string
    {
        return $this->themeConfiguration->getId();
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
