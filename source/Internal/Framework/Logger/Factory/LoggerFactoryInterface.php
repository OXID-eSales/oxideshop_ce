<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory;

use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    /**
     * @return LoggerInterface
     */
    public function create();
}
