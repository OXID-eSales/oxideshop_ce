<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Validator;

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\PsrLoggerConfigurationInterface;

/**
 * @internal
 */
interface LoggerConfigurationValidatorInterface
{
    /**
     * @param PsrLoggerConfigurationInterface $configuration
     */
    public function validate(PsrLoggerConfigurationInterface $configuration);
}
