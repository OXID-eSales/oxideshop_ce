<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class ThemeSettingChangedEvent extends Event
{
    const NAME = self::class;

    /**
     * Configuration variable that was changed.
     *
     * @var string
     */
    private $configurationVariable;

    /**
     * Shopid the configuration was changed for.
     *
     * @var integer
     */
    private $shopId;

    /**
     * Module information as in oxconfig.oxmodule
     *
     * @var string
     */
    private $theme;

    /**
     * ThemeSettingChangedEvent constructor.
     *
     * @param string $configurationVariable Config varname.
     * @param int    $shopId                Shop id.
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
     *
     * @return string
     */
    public function getConfigurationVariable(): string
    {
        return $this->configurationVariable;
    }

    /**
     * Getter for shop id.
     *
     * @return integer
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * Getter for theme information.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }
}
