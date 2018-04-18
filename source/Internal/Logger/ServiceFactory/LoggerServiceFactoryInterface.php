<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory;

use Psr\Log\LoggerInterface;

/**
 * @internal
 */
interface LoggerServiceFactoryInterface
{
    /**
     * @return LoggerInterface
     */
    public function create();
}
