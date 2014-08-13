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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Encrypts password together with salt, using setted encrypter
 */
class oxPasswordEncryptor
{
    /**
     * @var oxSha512Encryptor
     */
    private $_oEncryptor = null;

    /**
     * @param oxSha512Encryptor $oEncryptor encryptor
     */
    public function setEncryptor( $oEncryptor )
    {


        $this->_oEncryptor = $oEncryptor;

    }

    /**
     * @return oxSha512Encryptor
     */
    public function getEncryptor()
    {
        return $this->_oEncryptor;
    }

    /**
     * @param oxSha512Encryptor $oEncryptor - encryptor
     */
    public function __construct( $oEncryptor = null )
    {
        if (is_null($oEncryptor)) {
            $oEncryptor = oxNew('oxSha512Encryptor');
        }

        $this->setEncryptor( $oEncryptor );
    }

    /**
     * @param $sPassword
     * @param $sSalt
     *
     * @return oxSha512Encryptor
     */
    public function encrypt($sPassword, $sSalt)
    {
        return $this->getEncryptor()->encrypt($sPassword . $sSalt);
    }
}