<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration;

interface PsrLoggerConfigurationInterface
{
    /**
     * @return string
     */
    public function getLogLevel();
}
