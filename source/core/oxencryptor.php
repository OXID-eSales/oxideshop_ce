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
 * Class oxEncryptor
 */
class oxEncryptor
{

    /**
     * Encrypts string with given key.
     *
     * @param string $sString
     * @param string $sKey
     *
     * @return string
     */
    public function encrypt($sString, $sKey)
    {
        $sString = "ox{$sString}id";

        $sKey = $this->_formKey($sKey, $sString);

        $sString = $sString ^ $sKey;
        $sString = base64_encode($sString);
        $sString = str_replace("=", "!", $sString);

        return "ox_$sString";
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
