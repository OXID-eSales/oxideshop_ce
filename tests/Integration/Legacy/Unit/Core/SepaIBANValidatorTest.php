<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * oxSepaValidator test class
 *
 * Can validate:
 *  - IBAN (International Business Account Number)
 *  - IBAN Registry (all IBAN lengths by country)
 *  - BIC (Bank International Code)
 */
class SepaIBANValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test case to check setting of IBAN code lengths with custom data
     */
    public function testGetCodeLengths()
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $aCodeLengths = ["DE" => 22];

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertSame($aCodeLengths, $oSepaIBANValidator->getCodeLengths(), "IBAN code lengths must be set");
    }

    /**
     * Test case to check setting of IBAN code lengths with custom data
     */
    public function testIsValid_noCodeLengthsSetCorrectIBANGiven_false()
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $this->assertFalse($oSepaIBANValidator->isValid("MT84MALT011000012345MTLCAST001S"), "IBAN must be not valid");
    }

    /**
     * IBAN Registry data provider
     *
     * @return array
     */
    public function providerCodeLengths(): \Iterator
    {
        $sNotValidMsg = "IBAN code lengths must be not valid";
        $sValidMsg = "IBAN code lengths must be valid";
        yield [false, null, $sValidMsg];
        yield [false, ["AL", "GR", 33, 21], $sNotValidMsg];
        yield [false, ["GER" => 22], $sNotValidMsg];
        yield [false, ["DE" => "twotwo"], $sNotValidMsg];
        yield [false, ["de" => "22"], $sNotValidMsg];
        yield [false, ["EN" => "2.2"], $sNotValidMsg];
        yield [false, ["22" => "DE"], $sNotValidMsg];
        yield [false, [22 => "DE"], $sNotValidMsg];
        yield [true, ["DE" => "22"], $sValidMsg];
        yield [true, ["DE" => 22], $sValidMsg];
    }


    /**
     * Test case to check IBAN code lengths validation
     *
     * @dataProvider providerCodeLengths
     */
    public function testValidateCodeLengths($blExpected, $aCodeLengths, $sMessage)
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $this->assertEquals($blExpected, $oSepaIBANValidator->isValidCodeLengths($aCodeLengths), $sMessage);
    }

    /**
     * Test case to check IBAN code lengths validation
     *
     * @dataProvider providerCodeLengths
     */
    public function testSetCodeLengths($blExpected, $aCodeLengths, $sMessage)
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $this->assertEquals($blExpected, $oSepaIBANValidator->setCodeLengths($aCodeLengths), $sMessage);
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValid_validIBAN_true(): \Iterator
    {
        yield ["AL47212110090000000235698741", ['AL' => 28]];
        yield ["MT84MALT011000012345MTLCAST001S", ['MT' => 31]];
        yield ["NO9386011117947", ['NO' => 15]];
        yield ["NO9386011117947 ", ['NO' => 15]];
        yield [" NO9386011117947", ['NO' => 15]];
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValid_validIBAN_true
     */
    public function testIsValid_validIBAN_true($sIBAN, $aCodeLengths)
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertTrue($oSepaIBANValidator->isValid($sIBAN), "IBAN must be valid");
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValid_invalidIBAN_false(): \Iterator
    {
        yield ["_NO9386011117947", ['NO' => 15]];
        yield ["NX9386011117947", ['NX' => 15]];
        yield ["MT84MALT011000012345MTLCAST001S", ['MT' => 30]];
        yield ["MT84MALT011000012345MTLCAST001S", ['MT' => 32]];
        yield ["MT84MALT011000012345MTLCAST001S", ["DE" => 22]];
        yield ["MT84MALT011000012345MTLCAST001S", ["DE" => 31]];
        // Fix for bug entry 0005538: SEPA validator class IBAN validation issue
        yield ["1234567895", ['NO' => 15]];
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValid_invalidIBAN_false
     */
    public function testIsValid_invalidIBAN_false($sIBAN, $aCodeLengths)
    {
        $oSepaIBANValidator = oxNew('oxSepaIBANValidator');

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertFalse($oSepaIBANValidator->isValid($sIBAN), "IBAN must be not valid");
    }

    /**
     * @return array
     */
    protected function getTestCodeLengths()
    {
        return ['AL' => 28, 'DE' => 22, 'LT' => 20, 'MT' => 31, 'NO' => 15, 'NX' => 15];
    }
}
