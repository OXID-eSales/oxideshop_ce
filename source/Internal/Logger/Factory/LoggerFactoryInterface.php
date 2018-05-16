<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Factory;

use Psr\Log\LoggerInterface;

/**
 * @internal
 */
interface LoggerFactoryInterface
{
    /**
     * @return LoggerInterface
     */
    public function create();
}
