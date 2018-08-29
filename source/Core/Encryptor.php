<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
