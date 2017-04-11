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

class Unit_Core_oxCompanyVatInCountryCheckerTest extends OxidTestCase
{

    public function testGetCountry_set()
    {
        $oChecker = new oxCompanyVatInCountryChecker();
        $oCountry = new oxCountry();

        $oChecker->setCountry($oCountry);
        $this->assertSame($oCountry, $oChecker->getCountry());
    }

    public function testGetCountry_empty()
    {
        $oChecker = new oxCompanyVatInCountryChecker();
        $this->assertNull($oChecker->getCountry());
    }

    public function testValidate_countryNotSet()
    {
        $oChecker = new oxCompanyVatInCountryChecker();
        $oVatIn = new oxCompanyVatIn('DE1234');

        $this->assertFalse($oChecker->validate($oVatIn));
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($sCountryCode, $sVatIn, $blExpectValidationResult)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $oCountry = new oxCountry();
        $oCountry->oxcountry__oxvatinprefix = new oxField($sCountryCode);

        $oChecker = new oxCompanyVatInCountryChecker();
        $oChecker->setCountry($oCountry);

        $this->assertSame($blExpectValidationResult, $oChecker->validate($oVatIn));
    }

    public function validateDataProvider()
    {
        return array(
            array('LT', 'LT12345', true),
            array('LT', '', false),
            array('LT', '11', false),
            array('LT', 'ab', false),
            array('DE', 'LT12345', false),
        );
    }

    /**
     * Test for bug #4212
     */
    public function testValidateGreece()
    {
        $oVatIn = new oxCompanyVatIn('EL123');
        $oCountry = new oxCountry();
        $oCountry->load('a7c40f633114e8fc6.25257477');

        $oChecker = new oxCompanyVatInCountryChecker();
        $oChecker->setCountry($oCountry);
        $this->assertTrue($oChecker->validate($oVatIn));
    }

    public function testValidate_notValid_errorMessage()
    {
        $oVatIn = new oxCompanyVatIn('LT12345');
        $oCountry = new oxCountry();
        $oCountry->oxcountry__oxvatinprefix = new oxField('DE');

        $oChecker = new oxCompanyVatInCountryChecker();
        $oChecker->setCountry($oCountry);
        $oChecker->validate($oVatIn);

        $this->assertSame(oxCompanyVatInCountryChecker::ERROR_ID_NOT_VALID, $oChecker->getError());
    }

    public function testValidate_notValidWrongDataType_errorMessage()
    {
        $oVatIn = new oxCompanyVatIn('LT12345');
        $oCountry = new oxCountry();
        $oCountry->oxcountry__oxvatinprefix = new oxField(1);

        $oChecker = new oxCompanyVatInCountryChecker();
        $oChecker->setCountry($oCountry);
        $oChecker->validate($oVatIn);

        $this->assertSame(oxCompanyVatInCountryChecker::ERROR_ID_NOT_VALID, $oChecker->getError());
    }

    public function testValidate_valid_noErrorMessage()
    {
        $oVatIn = new oxCompanyVatIn('LT12345');
        $oCountry = new oxCountry();
        $oCountry->oxcountry__oxvatinprefix = new oxField('LT');

        $oChecker = new oxCompanyVatInCountryChecker();
        $oChecker->setCountry($oCountry);
        $oChecker->validate($oVatIn);

        $this->assertSame('', $oChecker->getError());
    }


}