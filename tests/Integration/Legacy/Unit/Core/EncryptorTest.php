<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Class Unit_Core_oxEncryptorTest
 */
class EncryptorTest extends \PHPUnit\Framework\TestCase
{
    public function providerEncodingAndDecoding(): \Iterator
    {
        // string encrypted with empty key
        yield ['testString', '', 'ox_MCcrOiwrDCstNjE4Njs!'];
        // string encrypted with numeric key
        yield ['testString', 1, 'ox_MEkrVCxFDEUtWDFWNlU!'];
        // string encrypted with not empty key
        yield ['testString', 'testKey', 'ox_MAwRFgc/Ng0tHQsUHS8!'];
        // empty string encrypted with not empty key
        yield ['', 'testKey', 'ox_MAwMFw!!'];
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
