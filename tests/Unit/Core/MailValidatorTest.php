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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

class MailValidatorTest extends \OxidTestCase
{

    public function providerValidateEmailWithValidEmail()
    {
        return array(
            array('mathias.krieck@oxid-esales.com'),
            array('mytest@com.org'),
            array('my+test@com.org'),
            array('mytest@oxid-esales.museum'),
        );
    }

    /**
     * @param $sEmail email to validate.
     *
     * @dataProvider providerValidateEmailWithValidEmail
     */
    public function testValidateEmailWithValidEmail($sEmail)
    {
        $oMailValidator = oxNew('oxMailValidator');
        $this->assertTrue($oMailValidator->isValidEmail($sEmail), 'Mail ' . $sEmail . ' validation failed. This mail is valid so should validate.');
    }

    public function providerValidateEmailWithNotValidEmail()
    {
        return array(
            array('?mathias.krieck@oxid-esales.com'),
            array('my/test@com.org'),
            array('@com.org'),
            array('mytestcom.org'),
            array('mytest@com'),
            array('info@�vyturys.lt'),
        );
    }

    /**
     * @param $sEmail email to validate.
     *
     * @dataProvider providerValidateEmailWithNotValidEmail
     */
    public function testValidateEmailWithNotValidEmail($sEmail)
    {
        $oMailValidator = oxNew('oxMailValidator');
        $this->assertFalse($oMailValidator->isValidEmail($sEmail), 'Mail ' . $sEmail . ' was valid. Should not be valid.');
    }

    public function testValidateEmailWithDifferentRuleSetFromConfig()
    {
        $sEmail = 'wrongemail';
        $this->setConfigParam('sEmailValidationRule', '/.*/');
        $oMailValidator = oxNew('oxMailValidator');
        $this->assertTrue($oMailValidator->isValidEmail($sEmail), 'Mail ' . $sEmail . ' was not valid. Should be valid with new rule.');
    }

    public function testValidateEmailWithDifferentRuleSetWithSetter()
    {
        $sEmail = 'wrongemail';
        $oMailValidator = oxNew('oxMailValidator');
        $oMailValidator->setMailValidationRule('/.*/');
        $this->assertTrue($oMailValidator->isValidEmail($sEmail), 'Mail ' . $sEmail . ' was not valid. Should be valid with new rule.');
    }

    public function testSetGetEmailValidationRule()
    {
        $oMailValidator = oxNew('oxMailValidator');
        $this->assertSame("/^([\w+\-.])+\@([\w\-.])+\.([A-Za-z]{2,64})$/i", $oMailValidator->getMailValidationRule(), 'Default mail validation rule is not as expected.');
        $sNewMailValidationRule = '/.*/';
        $oMailValidator->setMailValidationRule($sNewMailValidationRule);
        $this->assertSame($sNewMailValidationRule, $oMailValidator->getMailValidationRule(), 'Mail validation rule should be as set.');
    }
}
