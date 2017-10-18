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
class SepaBICValidatorTest extends \OxidTestCase
{

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_validBIC_true()
    {
        return array(
            array("ASPKAT2L"),
            array("AAAACCXX"),
            array("AAAACC22"),
            array("AAAACCXXHHH"),
            array("AAAACC33555"),
            array("AAAACCXX555"),
            array(" AAAACCXX"),
            array("AAAACCXX "),
            array("\tAAAACCXX"),
            array("AAAACCXX\n"),
            array("AAAACCXX\n\r"),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array("COBADEHD055"),
        );
    }


    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_validBIC_true
     */
    public function testIsValid_validBIC_true($sBIC)
    {
        $oSepaBICValidator = oxNew('oxSepaBICValidator');

        $this->assertTrue($oSepaBICValidator->isValid($sBIC), "BIC must be valid");
    }

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_invalidBIC_false()
    {
        return array(
            array("AAAACCX"),
            array("AAAACCXXX"),
            array("AAAACCXXXX"),
            array("AAAACC2233"),
            array("AAAACC2233*"),
            array("AAAACC224444X"),
            array("AAAACC224444XX"),
            array("AAA1CC22"),
            array("1AAAACXX"),
            array("A1AAACXX"),
            array("AA1AACXX"),
            array("AAA1ACXX"),
            array("AAAA1CXX"),
            array("AAAAC1XX"),
            array("AAAAC122"),
            array("ASPK AT 2L"),
            array("ASPK\tAT\t2L"),
            array("123 ASPKAT2L"),
            array("_ASPKAT2L"),
            array("ASPKAT2"),
            array("ASP_AT2L"),
            array("ASPK*T2L"),
            array("ASPKA-2L"),
            array("AAAßCCXX"),
            array("AAAACßXX"),
            array("AAAACCXö"),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array("123COBADEHD055ABC"),
        );
    }

    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_invalidBIC_false
     */
    public function testIsValid_invalidBIC_false($sBIC)
    {
        $oSepaBICValidator = oxNew('oxSepaBICValidator');

        $this->assertFalse($oSepaBICValidator->isValid($sBIC), "BIC must be not valid");
    }
}
