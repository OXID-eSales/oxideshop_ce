<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration;

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
