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
        return [['value1', true], [' value1 ', true], [null, false], ['', false], [' ', false], ['    ', false], [[], true], [['value1'], true], [['value1', 'value2'], true], [[null], false], [[''], false], [['', 'value2'], false], [['value1', ''], false]];
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
