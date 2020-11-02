<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartyConfigurationInterface
{
    /**
     * Return global smarty settings.
     */
    public function getSettings(): array;

    /**
     * Set global smarty settings.
     */
    public function setSettings(array $settings);

    /**
     * Return smarty security settings.
     */
    public function getSecuritySettings(): array;

    /**
     * Set smarty security settings.
     */
    public function setSecuritySettings(array $settings);

    /**
     * Return smarty plugins.
     */
    public function getPlugins(): array;

    /**
     * Set smarty plugins.
     */
    public function setPlugins(array $plugins);

    /**
     * Return smarty resources.
     */
    public function getResources(): array;

    /**
     * Set smarty resources.
     */
    public function setResources(array $resources);

    /**
     * Return smarty prefilters.
     */
    public function getPrefilters(): array;

    /**
     * Set smarty prefilters.
     */
    public function setPrefilters(array $prefilters);
}
