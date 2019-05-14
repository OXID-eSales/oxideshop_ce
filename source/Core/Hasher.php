<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Hasher abstract class
 *
 * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Authentication\Bridge\PasswordServiceBridgeInterface`
 *                                        was added as the new default for hashing passwords.
 */
abstract class Hasher
{
    /**
     * Hash string.
     *
     * @param string $string string for hashing.
     *
     * @return string
     */
    abstract public function hash($string);
}
