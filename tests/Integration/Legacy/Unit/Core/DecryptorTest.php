<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Class Unit_Core_oxEncryptorTest
 */
class DecryptorTest extends \OxidTestCase
{
    public function providerDecodingOfStringWithCorrectKey()
    {
        return [
            // string encrypted with empty key
            ['ox_MCcrOiwrDCstNjE4Njs!', '', 'testString'],
            // string encrypted with numeric key
            ['ox_MEkrVCxFDEUtWDFWNlU!', 1, 'testString'],
            // string encrypted with not empty key
            ['ox_MAwRFgc/Ng0tHQsUHS8!', 'testKey', 'testString'],
            // empty string encrypted with not empty key
            ['ox_MAwMFw!!', 'testKey', ''],
        ];
    }

    /**
     * @dataProvider providerDecodingOfStringWithCorrectKey
     */
    public function testDecodingOfStringWithCorrectKey($sEncodedString, $sKey, $sString)
    {
        $oDecryptor = oxNew('oxDecryptor');

        $this->assertSame($sString, $oDecryptor->decrypt($sEncodedString, $sKey));
    }

    public function testDecodingOfStringWithIncorrectKey()
    {
        $oDecryptor = oxNew('oxDecryptor');

        $this->assertNotSame('testString', $oDecryptor->decrypt('ox_Gx0HETgRKgAXGhosDB0!', 'incorrectKey'));
    }
}
