<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class ShopConfigurationChangedEvent extends Event
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
     * ShopConfigurationChangedEvent constructor.
     *
     * @param string $configurationVariable config varname
     * @param int    $shopId                shop id
     */
    public function __construct(string $configurationVariable, int $shopId)
    {
        $this->configurationVariable = $configurationVariable;
        $this->shopId = $shopId;
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
}
