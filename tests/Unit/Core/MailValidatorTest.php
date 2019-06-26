<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

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
