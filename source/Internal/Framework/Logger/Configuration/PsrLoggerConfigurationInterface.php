<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration;

/**
 * @deprecated will be removed in next major version
 */
interface PsrLoggerConfigurationInterface
{
    /**
     * @return string
     */
    public function getLogLevel();
}
