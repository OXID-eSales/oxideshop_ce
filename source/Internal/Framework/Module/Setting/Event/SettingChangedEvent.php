<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SettingChangedEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event
 */
class SettingChangedEvent extends Event
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
    private $module;

    /**
     * SettingChangedEvent constructor.
     *
     * @param string $configurationVariable Config varname.
     * @param int    $shopId                Shop id.
     * @param string $module                Module information as in oxconfig.oxmodule
     */
    public function __construct(string $configurationVariable, int $shopId, string $module)
    {
        $this->configurationVariable = $configurationVariable;
        $this->shopId = $shopId;
        $this->module = $module;
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
     * Getter for module information.
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }
}
