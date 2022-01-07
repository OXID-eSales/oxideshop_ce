<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements;

interface SystemSecurityCheckerInterface
{
    /**
     * Checks whether system is configured to access an appropriate source of randomness for
     * Cryptographically-Secure PseudoRandom Number Generators.
     * @return bool
     */
    public function isCryptographicallySecure(): bool;
}
