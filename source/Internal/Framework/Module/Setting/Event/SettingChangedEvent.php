<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class SettingChangedEvent extends Event
{
    const NAME = self::class;

    /**
     * @var string
     */
    private $settingName;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string
     */
    private $moduleId;

    public function __construct(string $settingName, int $shopId, string $moduleId)
    {
        $this->settingName = $settingName;
        $this->shopId = $shopId;
        $this->moduleId = $moduleId;
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
