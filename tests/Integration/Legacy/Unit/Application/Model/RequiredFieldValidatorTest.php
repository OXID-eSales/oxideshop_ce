<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

/**
 * Testing oxRequiredFieldValidator class.
 */
class RequiredFieldValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function providerValidateFieldValue(): \Iterator
    {
        yield ['value1', true];
        yield [' value1 ', true];
        yield [null, false];
        yield ['', false];
        yield [' ', false];
        yield ['    ', false];
        yield [[], true];
        yield [['value1'], true];
        yield [['value1', 'value2'], true];
        yield [[null], false];
        yield [[''], false];
        yield [['', 'value2'], false];
        yield [['value1', ''], false];
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
