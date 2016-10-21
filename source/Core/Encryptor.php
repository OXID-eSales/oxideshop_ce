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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class oxEncryptor
 */
class Encryptor
{

    /**
     * Encrypts string with given key.
     *
     * @param string $string
     * @param string $key
     *
     * @return string
     */
    public function encrypt($string, $key)
    {
        $string = "ox{$string}id";

        $key = $this->_formKey($key, $string);

        $string = $string ^ $key;
        $string = base64_encode($string);
        $string = str_replace("=", "!", $string);

        return "ox_$string";
    }

    /**
     * Forms key for use in encoding.
     *
     * @param string $key
     * @param string $string
     *
     * @return string
     */
    protected function _formKey($key, $string)
    {
        $key = '_' . $key;
        $keyLength = (strlen($string) / strlen($key)) + 5;

        return str_repeat($key, $keyLength);
    }
}
