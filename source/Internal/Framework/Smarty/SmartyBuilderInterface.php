<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

interface SmartyBuilderInterface
{
    /**
     * Sets properties of smarty object.
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSettings(array $settings);

    /**
     * Sets security options of smarty object.
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSecuritySettings(array $settings);

    /**
     * Registers a resource of smarty object.
     *
     * @param array $resourcesToRegister
     *
     * @return self
     */
    public function registerResources(array $resourcesToRegister);

    /**
     * Register prefilters of smarty object.
     *
     * @param array $prefilters
     *
     * @return self
     */
    public function registerPrefilters(array $prefilters);

    /**
     * Register plugins of smarty object.
     *
     * @param array $plugins
     *
     * @return self
     */
    public function registerPlugins(array $plugins);

    /**
     * @return \Smarty
     */
    public function getSmarty(): \Smarty;
}
