<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class SettingChangedEvent extends Event
{
    public const NAME = self::class;

    public function __construct(
        private string $settingName,
        private int $shopId,
        private string $moduleId
    ) {
    }

    public function getSettingName(): string
    {
        return $this->settingName;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }
}
