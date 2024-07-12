<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Class Unit_Core_oxEncryptorTest
 */
class EncryptorTest extends \OxidTestCase
{
    public function providerEncodingAndDecoding()
    {
        return [
            // string encrypted with empty key
            ['testString', '', 'ox_MCcrOiwrDCstNjE4Njs!'],
            // string encrypted with numeric key
            ['testString', 1, 'ox_MEkrVCxFDEUtWDFWNlU!'],
            // string encrypted with not empty key
            ['testString', 'testKey', 'ox_MAwRFgc/Ng0tHQsUHS8!'],
            // empty string encrypted with not empty key
            ['', 'testKey', 'ox_MAwMFw!!'],
        ];
    }

    /**
     * @dataProvider providerEncodingAndDecoding
     *
     * @param $sString
     * @param $sKey
     * @param $sEncodedString
     */
    public function testEncodingAndDecoding($sString, $sKey, $sEncodedString)
    {
        $oEncryptor = oxNew('oxEncryptor');

        $this->assertSame($sEncodedString, $oEncryptor->encrypt($sString, $sKey));
    }
}
