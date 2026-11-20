<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ThemeSettingTypeChangedEvent extends Event
{
    public function __construct(
        private readonly int $shopId,
        private readonly string $themeId,
        private readonly string $settingName,
        private readonly string $previousType,
        private readonly string $type
    ) {
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function getSettingName(): string
    {
        return $this->settingName;
    }

    public function getPreviousType(): string
    {
        return $this->previousType;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
