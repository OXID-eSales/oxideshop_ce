<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Encryptor;

/**
 * Class Unit_Core_oxEncryptorTest
 */
class EncryptationTest extends \OxidTestCase
{
    public function providerEncodingAndDecodingGivesSameResultWithCorrectKey()
    {
        return array(
            array('testString', ''),
            array('testString', 1),
            array('testString', 'shortKey'),
            array('testString', 'longKeyLongKey_LongKeyLongKey'),
            array('', 'testKey'),
        );
    }

    /**
     * @dataProvider providerEncodingAndDecodingGivesSameResultWithCorrectKey
     */
    public function testEncodingAndDecodingGivesSameResultWithCorrectKey($sString, $sKey)
    {
        $oEncryptor = oxNew('oxEncryptor');
        $oDecryptor = oxNew('oxDecryptor');

        $sEncrypted = $oEncryptor->encrypt($sString, $sKey);
        $this->assertSame($sString, $oDecryptor->decrypt($sEncrypted, $sKey));
    }

    public function testEncodingAndDecodingGivesDifferentResultWithIncorrectKey()
    {
        $oEncryptor = oxNew('oxEncryptor');
        $oDecryptor = oxNew('oxDecryptor');

        $sEncrypted = $oEncryptor->encrypt('testString', 'correctKey');
        $this->assertNotSame('testString', $oDecryptor->decrypt($sEncrypted, 'incorrectKey'));
    }
}
