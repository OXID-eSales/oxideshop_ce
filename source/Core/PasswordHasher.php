<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Hash password together with salt, using set hash algorithm
 *
 * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Authentication\Bridge\PasswordServiceBridgeInterface`
 *                                        was added as the new default for hashing passwords.
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
     */
    protected function _getHasher()
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
