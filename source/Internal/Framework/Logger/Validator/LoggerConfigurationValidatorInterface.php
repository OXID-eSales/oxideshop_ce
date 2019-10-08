<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;

interface LoggerConfigurationValidatorInterface
{
    /**
     * @param PsrLoggerConfigurationInterface $configuration
     */
    public function validate(PsrLoggerConfigurationInterface $configuration);
}
