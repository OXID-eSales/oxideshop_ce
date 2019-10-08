<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Set global smarty settings.
     *
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array
    {
        return $this->securitySettings;
    }

    /**
     * Set smarty security settings.
     *
     * @param array $settings
     */
    public function setSecuritySettings(array $settings)
    {
        $this->securitySettings = $settings;
    }

    /**
     * Return smarty plugins.
     *
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * Set smarty plugins.
     *
     * @param array $plugins
     */
    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * Return smarty resources.
     *
     * @return array
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Set smarty resources.
     *
     * @param array $resources
     */
    public function setResources(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Return smarty prefilters.
     *
     * @return array
     */
    public function getPrefilters(): array
    {
        return $this->prefilters;
    }

    /**
     * Set smarty prefilters.
     *
     * @param array $prefilters
     */
    public function setPrefilters(array $prefilters)
    {
        $this->prefilters = $prefilters;
    }
}
