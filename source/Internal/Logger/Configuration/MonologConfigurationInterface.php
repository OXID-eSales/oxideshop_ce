<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Configuration;

/**
 * @internal
 */
interface MonologConfigurationInterface extends PsrLoggerConfigurationInterface
{
    /**
     * @return string
     */
    public function getLoggerName();

    /**
     * @return string
     */
    public function getLogFilePath();
}
