<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;

interface LoggerConfigurationValidatorInterface
{
    public function validate(PsrLoggerConfigurationInterface $configuration);
}
