<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

/**
 * Testing oxRequiredFieldValidator class.
 */
class RequiredFieldValidatorTest extends \OxidTestCase
{
    public function providerValidateFieldValue()
    {
        return array(
            array('value1', true),
            array(' value1 ', true),
            array(null, false),
            array('', false),
            array(' ', false),
            array('    ', false),
            array(array(), true),
            array(array('value1'), true),
            array(array('value1', 'value2'), true),
            array(array(null), false),
            array(array(''), false),
            array(array('', 'value2'), false),
            array(array('value1', ''), false),
        );
    }

    /**
     * @param string $sString
     * @param bool   $blResult
     *
     * @dataProvider providerValidateFieldValue
     */
    public function testValidateFieldValue($sString, $blResult)
    {
        $oAddressValidator = oxNew('oxRequiredFieldValidator');
        $this->assertSame($blResult, $oAddressValidator->validateFieldValue($sString));
    }
}
