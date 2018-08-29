<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Generates Salt for the user password
 */
class PasswordSaltGenerator
{
    /**
     * @var \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker
     */
    private $_openSSLFunctionalityChecker;

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker $openSSLFunctionalityChecker
     */
    public function __construct(\OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker $openSSLFunctionalityChecker)
    {
        $this->_openSSLFunctionalityChecker = $openSSLFunctionalityChecker;
    }

    /**
     * Generates salt. If openssl_random_pseudo_bytes function is not available,
     * than fallback to custom salt generator.
     *
     * @return string
     */
    public function generate()
    {
        if ($this->_getOpenSSLFunctionalityChecker()->isOpenSslRandomBytesGeneratorAvailable()) {
            $sSalt = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $sSalt = $this->_customSaltGenerator();
        }

        return $sSalt;
    }

    /**
     * Gets open SSL functionality checker.
     *
     * @return \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker
     */
    protected function _getOpenSSLFunctionalityChecker()
    {
        return $this->_openSSLFunctionalityChecker;
    }

    /**
     * Generates custom salt.
     *
     * @return string
     */
    protected function _customSaltGenerator()
    {
        $sHash = '';
        $sSalt = '';
        for ($i = 0; $i < 32; $i++) {
            $sHash = hash('sha256', $sHash . mt_rand());
            $iPosition = mt_rand(0, 62);
            $sSalt .= $sHash[$iPosition];
        }

        return $sSalt;
    }
}
