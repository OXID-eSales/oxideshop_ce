<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Validates if there are no Javascript.
 */
class NoJsValidator
{
    /**
     * Checks if provided config value is not vulnerable.
     *
     * @param string $configValue
     *
     * @return bool
     */
    public function isValid($configValue)
    {
        return preg_match('/<script.*>/', $configValue) === 0;
    }
}
