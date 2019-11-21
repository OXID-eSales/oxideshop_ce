<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory;

use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    /**
     * @return LoggerInterface
     */
    public function create();
}
