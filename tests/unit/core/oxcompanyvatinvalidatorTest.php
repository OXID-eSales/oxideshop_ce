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

class Unit_Core_oxCompanyVatInValidatorTest extends OxidTestCase
{

    public function testGetCountry_Construct()
    {
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertSame($oCountry, $oValidator->getCountry());
    }

    public function testGetCountry_set()
    {
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oCountryOther = new oxCountry();
        $oValidator->setCountry($oCountryOther);

        $this->assertSame($oCountryOther, $oValidator->getCountry());
    }

    public function testGetError_notSet()
    {
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertSame('', $oValidator->getError());
    }

    public function testGetError_set()
    {
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);
        $oValidator->setError('Error');

        $this->assertSame('Error', $oValidator->getError());
    }

    public function testAddChecker()
    {
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = new oxCompanyVatInCountryChecker();
        $oValidator->addChecker($oChecker);

        $this->assertSame(1, count($oValidator->getCheckers()));
    }

    public function testValidate_noCheckers()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertFalse($oValidator->validate($oVatIn));
    }

    public function testValidate_onChecker_success()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oValidator->addChecker($oChecker);

        $this->assertTrue($oValidator->validate($oVatIn));
    }

    public function testValidate_onChecker_fail()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker->expects($this->any())->method('validate')->will($this->returnValue(false));
        $oChecker->expects($this->any())->method('getError')->will($this->returnValue('Error'));


        $oValidator->addChecker($oChecker);

        $this->assertFalse($oValidator->validate($oVatIn));
        $this->assertSame('Error', $oValidator->getError());
    }

    public function testValidate_2Checkers_success()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oValidator->addChecker($oChecker);

        $oChecker2 = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker2->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oValidator->addChecker($oChecker2);

        $this->assertTrue($oValidator->validate($oVatIn));
    }

    public function testValidate_2Checkers_OneFail()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = new oxCountry();
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oValidator->addChecker($oChecker);

        $oChecker2 = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker2->expects($this->any())->method('validate')->will($this->returnValue(false));
        $oChecker2->expects($this->any())->method('getError')->will($this->returnValue('Error'));

        $oValidator->addChecker($oChecker2);

        $this->assertFalse($oValidator->validate($oVatIn));
        $this->assertSame('Error', $oValidator->getError());
    }
}