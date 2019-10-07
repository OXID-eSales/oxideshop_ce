<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartyConfigurationInterface
{
    /**
     * Return global smarty settings.
     *
     * @return array
     */
    public function getSettings(): array;

    /**
     * Set global smarty settings.
     *
     * @param array $settings
     */
    public function setSettings(array $settings);

    /**
     * Return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array;

    /**
     * Set smarty security settings.
     *
     * @param array $settings
     */
    public function setSecuritySettings(array $settings);

    /**
     * Return smarty plugins.
     *
     * @return array
     */
    public function getPlugins(): array;

    /**
     * Set smarty plugins.
     *
     * @param array $plugins
     */
    public function setPlugins(array $plugins);

    /**
     * Return smarty resources.
     *
     * @return array
     */
    public function getResources(): array;

    /**
     * Set smarty resources.
     *
     * @param array $resources
     */
    public function setResources(array $resources);

    /**
     * Return smarty prefilters.
     *
     * @return array
     */
    public function getPrefilters(): array;

    /**
     * Set smarty prefilters.
     *
     * @param array $prefilters
     */
    public function setPrefilters(array $prefilters);
}
