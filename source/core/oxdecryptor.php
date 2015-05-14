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
 * Class oxDecryptor
 */
class oxDecryptor
{

    /**
     * Decrypts string with given key.
     *
     * @param string $sString string
     * @param string $sKey    key
     *
     * @return string
     */
    public function decrypt($sString, $sKey)
    {
        $sKey = $this->_formKey($sKey, $sString);

        $sString = substr($sString, 3);
        $sString = str_replace('!', '=', $sString);
        $sString = base64_decode($sString);
        $sString = $sString ^ $sKey;

        return substr($sString, 2, -2);
    }

    /**
     * Forms key for use in encoding.
     *
     * @param string $sKey
     * @param string $sString
     *
     * @return string
     */
    protected function _formKey($sKey, $sString)
    {
        $sKey = '_' . $sKey;
        $iKeyLength = (strlen($sString) / strlen($sKey)) + 5;

        return str_repeat($sKey, $iKeyLength);
    }
}
