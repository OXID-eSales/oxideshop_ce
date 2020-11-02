<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

class SmartyConfiguration implements SmartyConfigurationInterface
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var array
     */
    private $prefilters = [];

    /**
     * @var array
     */
    private $securitySettings = [];

    /**
     * Return global smarty settings.
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Set global smarty settings.
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Return smarty security settings.
     */
    public function getSecuritySettings(): array
    {
        return $this->securitySettings;
    }

    /**
     * Set smarty security settings.
     */
    public function setSecuritySettings(array $settings): void
    {
        $this->securitySettings = $settings;
    }

    /**
     * Return smarty plugins.
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * Set smarty plugins.
     */
    public function setPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }

    /**
     * Return smarty resources.
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Set smarty resources.
     */
    public function setResources(array $resources): void
    {
        $this->resources = $resources;
    }

    /**
     * Return smarty prefilters.
     */
    public function getPrefilters(): array
    {
        return $this->prefilters;
    }

    /**
     * Set smarty prefilters.
     */
    public function setPrefilters(array $prefilters): void
    {
        $this->prefilters = $prefilters;
    }
}
