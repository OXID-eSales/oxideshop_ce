<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Configuration;

/**
 * @internal
 */
interface PsrLoggerConfigurationInterface
{
    /**
     * @return string
     */
    public function getLogLevel();
}
