<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;

/**
 * @deprecated will be removed in next major version
 */
interface LoggerConfigurationValidatorInterface
{
    /**
     * @param PsrLoggerConfigurationInterface $configuration
     */
    public function validate(PsrLoggerConfigurationInterface $configuration);
}
