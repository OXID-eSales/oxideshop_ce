<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxCompanyVatInValidator;
use \oxCompanyVatIn;

class CompanyVatInValidatorTest extends \OxidTestCase
{
    public function testGetCountry_Construct()
    {
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertSame($oCountry, $oValidator->getCountry());
    }

    public function testGetCountry_set()
    {
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oCountryOther = oxNew('oxCountry');
        $oValidator->setCountry($oCountryOther);

        $this->assertSame($oCountryOther, $oValidator->getCountry());
    }

    public function testGetError_notSet()
    {
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertSame('', $oValidator->getError());
    }

    public function testGetError_set()
    {
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);
        $oValidator->setError('Error');

        $this->assertSame('Error', $oValidator->getError());
    }

    public function testAddChecker()
    {
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = oxNew('oxCompanyVatInCountryChecker');
        $oValidator->addChecker($oChecker);

        $this->assertSame(1, count($oValidator->getCheckers()));
    }

    public function testValidate_noCheckers()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $this->assertFalse($oValidator->validate($oVatIn));
    }

    public function testValidate_onChecker_success()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = oxNew('oxCountry');
        $oValidator = new oxCompanyVatInValidator($oCountry);

        $oChecker = $this->getMock('oxCompanyVatInCountryChecker');
        $oChecker->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oValidator->addChecker($oChecker);

        $this->assertTrue($oValidator->validate($oVatIn));
    }

    public function testValidate_onChecker_fail()
    {
        $oVatIn = new oxCompanyVatIn('LT1123');
        $oCountry = oxNew('oxCountry');
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
        $oCountry = oxNew('oxCountry');
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
        $oCountry = oxNew('oxCountry');
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
