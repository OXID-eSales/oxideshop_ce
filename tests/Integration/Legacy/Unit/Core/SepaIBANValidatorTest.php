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

        $this->assertEquals($aCodeLengths, $oSepaIBANValidator->getCodeLengths(), "IBAN code lengths must be set");
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
    public function providerCodeLengths()
    {
        $sNotValidMsg = "IBAN code lengths must be not valid";
        $sValidMsg = "IBAN code lengths must be valid";

        return [[false, null, $sValidMsg], [false, ["AL", "GR", 33, 21], $sNotValidMsg], [false, ["GER" => 22], $sNotValidMsg], [false, ["DE" => "twotwo"], $sNotValidMsg], [false, ["de" => "22"], $sNotValidMsg], [false, ["EN" => "2.2"], $sNotValidMsg], [false, ["22" => "DE"], $sNotValidMsg], [false, [22 => "DE"], $sNotValidMsg], [true, ["DE" => "22"], $sValidMsg], [true, ["DE" => 22], $sValidMsg]];
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
    public function providerIsValid_validIBAN_true()
    {
        return [["AL47212110090000000235698741", ['AL' => 28]], ["MT84MALT011000012345MTLCAST001S", ['MT' => 31]], ["NO9386011117947", ['NO' => 15]], ["NO9386011117947 ", ['NO' => 15]], [" NO9386011117947", ['NO' => 15]]];
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
    public function providerIsValid_invalidIBAN_false()
    {
        return [
            ["_NO9386011117947", ['NO' => 15]],
            ["NX9386011117947", ['NX' => 15]],
            ["MT84MALT011000012345MTLCAST001S", ['MT' => 30]],
            ["MT84MALT011000012345MTLCAST001S", ['MT' => 32]],
            ["MT84MALT011000012345MTLCAST001S", ["DE" => 22]],
            ["MT84MALT011000012345MTLCAST001S", ["DE" => 31]],
            // Fix for bug entry 0005538: SEPA validator class IBAN validation issue
            ["1234567895", ['NO' => 15]],
        ];
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
