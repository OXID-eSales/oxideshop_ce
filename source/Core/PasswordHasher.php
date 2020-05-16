<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Hash password together with salt, using set hash algorithm
 *
 * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
 *                                        was added as the new default for hashing passwords. Hashing passwords with
 *                                        MD5 and SHA512 is still supported in order support login with older
 *                                        password hashes. Therefor this class might not be
 *                                        compatible with the current passhword hash any more.
 */
class PasswordHasher
{
    /**
     * @var \oxHasher
     */
    private $_ohasher = null;

    /**
     * Gets hasher.
     *
     * @return \OxidEsales\Eshop\Core\Hasher
     * @deprecated underscore prefix violates PSR12, will be renamed to "getHasher" in next major
     */
    protected function _getHasher() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->_ohasher;
    }

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\Hasher $oHasher hasher.
     */
    public function __construct($oHasher)
    {
        $this->_ohasher = $oHasher;
    }

    /**
     * Hash password with a salt.
     *
     * @param string $sPassword not hashed password.
     * @param string $sSalt     salt string.
     *
     * @return string
     */
    public function hash($sPassword, $sSalt)
    {
        return $this->_getHasher()->hash($sPassword . $sSalt);
    }
}
