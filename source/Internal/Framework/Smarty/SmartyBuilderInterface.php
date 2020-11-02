<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

interface SmartyBuilderInterface
{
    /**
     * Sets properties of smarty object.
     *
     * @return self
     */
    public function setSettings(array $settings);

    /**
     * Sets security options of smarty object.
     *
     * @return self
     */
    public function setSecuritySettings(array $settings);

    /**
     * Registers a resource of smarty object.
     *
     * @return self
     */
    public function registerResources(array $resourcesToRegister);

    /**
     * Register prefilters of smarty object.
     *
     * @return self
     */
    public function registerPrefilters(array $prefilters);

    /**
     * Register plugins of smarty object.
     *
     * @return self
     */
    public function registerPlugins(array $plugins);

    public function getSmarty(): \Smarty;
}
