<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Generates Salt for the user password
 *
 */
class oxPasswordSaltGenerator
{

    /**
     * @var oxOpenSSLFunctionalityChecker
     */
    private $_openSSLFunctionalityChecker;

    /**
     * Sets dependencies.
     *
     * @param oxOpenSSLFunctionalityChecker $openSSLFunctionalityChecker
     */
    public function __construct(oxOpenSSLFunctionalityChecker $openSSLFunctionalityChecker)
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
     * @return oxOpenSSLFunctionalityChecker
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
