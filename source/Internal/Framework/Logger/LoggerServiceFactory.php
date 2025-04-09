<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

readonly class LoggerServiceFactory
{
    public function __construct(private ContextInterface $context)
    {
    }

    public function getLogger(): LoggerInterface
    {
        return (new MonologLoggerFactory(
            'OXID Logger',
            $this->context->getLogFilePath(),
            $this->context->getLogLevel()
        ))
            ->create();
    }
}
