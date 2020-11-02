<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class ThemeSettingChangedEvent extends Event
{
    public const NAME = self::class;

    /**
     * Configuration variable that was changed.
     *
     * @var string
     */
    private $configurationVariable;

    /**
     * Shopid the configuration was changed for.
     *
     * @var int
     */
    private $shopId;

    /**
     * Module information as in oxconfig.oxmodule.
     *
     * @var string
     */
    private $theme;

    /**
     * ThemeSettingChangedEvent constructor.
     *
     * @param string $configurationVariable config varname
     * @param int    $shopId                shop id
     * @param string $theme                 Theme information as in oxconfig.oxmodule
     */
    public function __construct(string $configurationVariable, int $shopId, string $theme)
    {
        $this->configurationVariable = $configurationVariable;
        $this->shopId = $shopId;
        $this->theme = $theme;
    }

    /**
     * Getter for configuration variable name.
     */
    public function getConfigurationVariable(): string
    {
        return $this->configurationVariable;
    }

    /**
     * Getter for shop id.
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * Getter for theme information.
     */
    public function getTheme(): string
    {
        return $this->theme;
    }
}
