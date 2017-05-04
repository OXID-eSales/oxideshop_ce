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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class Unit_Core_oxEncryptorTest
 */
class Unit_Core_oxDecryptorTest extends OxidTestCase
{

    public function providerDecodingOfStringWithCorrectKey()
    {
        return array(
            // string encrypted with empty key
            array('ox_MCcrOiwrDCstNjE4Njs!', '', 'testString'),
            // string encrypted with numeric key
            array('ox_MEkrVCxFDEUtWDFWNlU!', 1, 'testString'),
            // string encrypted with not empty key
            array('ox_MAwRFgc/Ng0tHQsUHS8!', 'testKey', 'testString'),
            // empty string encrypted with not empty key
            array('ox_MAwMFw!!', 'testKey', ''),
        );
    }

    /**
     * @dataProvider providerDecodingOfStringWithCorrectKey
     */
    public function testDecodingOfStringWithCorrectKey($sEncodedString, $sKey, $sString)
    {
        $oDecryptor = new oxDecryptor();

        $this->assertSame($sString, $oDecryptor->decrypt($sEncodedString, $sKey));
    }

    public function testDecodingOfStringWithIncorrectKey()
    {
        $oDecryptor = new oxDecryptor();

        $this->assertNotSame('testString', $oDecryptor->decrypt('ox_Gx0HETgRKgAXGhosDB0!', 'incorrectKey'));
    }
}