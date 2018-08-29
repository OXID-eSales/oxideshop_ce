<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Encrypt string with sha512 algorithm.
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
