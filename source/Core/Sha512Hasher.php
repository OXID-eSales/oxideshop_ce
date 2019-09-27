<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Encrypt string with sha512 algorithm.
 *
 * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
 *                                        was added as the new default for hashing passwords. Hashing passwords with
 *                                        MD5 and SHA512 is still supported in order support login with older
 *                                        password hashes. Therefor this class might not be
 *                                        compatible with the current passhword hash any more.
 */
class Sha512Hasher extends \OxidEsales\Eshop\Core\Hasher
{
    /** Algorithm name. */
    const HASHING_ALGORITHM_SHA512 = 'sha512';

    /**
     * Encrypt string.
     *
     * @param string $string
     *
     * @return string
     */
    public function hash($string)
    {
        return hash(self::HASHING_ALGORITHM_SHA512, $string);
    }
}
