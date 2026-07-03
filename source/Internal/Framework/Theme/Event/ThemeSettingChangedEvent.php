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
 * @see OxidEsales/EshopCommunity/Internal/README.md
 *
 * @deprecated since v7.6.0. This class will be removed in version 8.0.
 */
class ThemeSettingChangedEvent extends Event
{
    /**
     * @param string $theme Theme information as in oxconfig.oxmodule
     */
    public function __construct(
        private string $configurationVariable,
        private int $shopId,
        private string $theme
    ) {
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
