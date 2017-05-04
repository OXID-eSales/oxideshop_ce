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

class Unit_Core_oxCompanyVatInTest extends OxidTestCase
{

    public function testConstruct()
    {
        $oVatIn = new oxCompanyVatIn('LT12345');
        $this->assertSame('LT12345', (string) $oVatIn);
    }

    public function testConstruct_empty()
    {
        $oVatIn = new oxCompanyVatIn('');
        $this->assertSame('', (string) $oVatIn);
    }

    /**
     * @dataProvider vatInProviderForCountryCode
     */
    public function testGetVatInCountryCode($sVatIn, $sExpectCode)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $this->assertSame($sExpectCode, $oVatIn->getCountryCode());
    }

    public function vatInProviderForCountryCode()
    {
        return array(
            array('LT12345', 'LT'),
            array(' LT12345', 'LT'),
            array('lt12345', 'LT'),
            array('LT 12345', 'LT'),
            array('LT-12 345', 'LT'),
            array('LT.123.45', 'LT'),
            array('LT,123,45', 'LT'),
            array('', ''),
            array(null, ''),
            array(1, '1'),
            array('1111', '11'),
            array('abcd', 'AB')
        );
    }

    /**
     * @dataProvider vatInProviderForNumbers
     */
    public function testGetVatInNumbers($sVatIn, $sExpectCode)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $this->assertSame($sExpectCode, $oVatIn->getNumbers());
    }

    public function vatInProviderForNumbers()
    {
        return array(
            array('LT12345', '12345'),
            array(' LT12345', '12345'),
            array('LT12345 ', '12345'),
            array(' LT12345 ', '12345'),
            array('', ''),
            array('1111', '11'),
            array('abcd', 'cd'),
            array('LT 12345', '12345'),
            array('LT-12 345', '12345'),
            array('LT.123.45', '.123.45'),
            array('LT,123,45', ',123,45'),
        );
    }

}