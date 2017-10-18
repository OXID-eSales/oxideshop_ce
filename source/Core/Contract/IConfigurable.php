<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * The interface methods should be implemented by classes which need a configuration object
 * (usually OxConfig) manually set.
 */
interface IConfigurable
{

    /**
     * Sets configuration object
     *
     * @param \OxidEsales\Eshop\Core\Config $oConfig Configraution object
     *
     * @abstract
     *
     * @return mixed
     */
    public function setConfig(\OxidEsales\Eshop\Core\Config $oConfig);

    /**
     * Returns active configuration object
     *
     * @abstract
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public function getConfig();
}
